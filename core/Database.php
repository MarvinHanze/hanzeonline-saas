<?php
declare(strict_types=1);

namespace Core;

class Database
{
    private static ?\PDO $pdo = null;
    private static ?int $tenantId = null;

    public static function connect(): \PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
            self::$pdo = new \PDO($dsn, $config['user'], $config['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }

    public static function setTenant(int $tenantId): void
    {
        self::$tenantId = $tenantId;
    }

    public static function getTenantId(): ?int
    {
        return self::$tenantId;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        return self::query($sql, $params)->fetch() ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        self::query($sql, array_values($data));
        return (int) self::connect()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): void
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $where";
        self::query($sql, array_merge(array_values($data), $whereParams));
    }

    public static function delete(string $table, string $where, array $params = []): void
    {
        self::query("DELETE FROM $table WHERE $where", $params);
    }

    public static function count(string $table, string $where = '1=1', array $params = []): int
    {
        return (int) self::fetch("SELECT COUNT(*) as c FROM $table WHERE $where", $params)['c'];
    }

    /**
     * Checkt of een kolom al bestaat (voor idempotente ALTER TABLE-migraties zonder
     * migratie-tooling — we hebben geen CLI/composer beschikbaar in deze omgeving).
     */
    public static function columnExists(string $table, string $column): bool
    {
        $row = self::fetch(
            "SELECT COUNT(*) as c FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?",
            [$table, $column]
        );
        return (int) ($row['c'] ?? 0) > 0;
    }

    public static function tableExists(string $table): bool
    {
        $row = self::fetch(
            "SELECT COUNT(*) as c FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?",
            [$table]
        );
        return (int) ($row['c'] ?? 0) > 0;
    }

    /**
     * Voegt een kolom toe als die nog niet bestaat. $definition is de ruwe SQL
     * na de kolomnaam, bv. "VARCHAR(500) NULL".
     */
    public static function ensureColumn(string $table, string $column, string $definition): void
    {
        if (!self::columnExists($table, $column)) {
            self::connect()->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    }

    /**
     * Verbreedt tenants.plan van de oorspronkelijke ENUM naar VARCHAR zodat nieuwe
     * plan-slugs (freemium, business, ...) niet worden geweigerd door de kolom.
     * Alleen uitgevoerd als de kolom nog daadwerkelijk een enum is (idempotent).
     */
    private static function ensurePlanColumnIsVarchar(): void
    {
        $row = self::fetch(
            "SELECT DATA_TYPE FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'tenants' AND column_name = 'plan'"
        );
        if ($row && strtolower((string) $row['DATA_TYPE']) === 'enum') {
            self::connect()->exec("ALTER TABLE tenants MODIFY COLUMN plan VARCHAR(50) NOT NULL DEFAULT 'starter'");
        }
    }

    public static function initSchema(): void
    {
        $pdo = self::connect();

        $pdo->exec("CREATE TABLE IF NOT EXISTS tenants (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            plan VARCHAR(50) DEFAULT 'starter',
            settings JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            role ENUM('owner','admin','user') DEFAULT 'user',
            avatar VARCHAR(500),
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_email_tenant (email, tenant_id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            plan VARCHAR(50) NOT NULL,
            status ENUM('active','trialing','past_due','cancelled') DEFAULT 'active',
            started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // --- SaaS-platform uitbreiding: per-tenant module-activatie ---
        $pdo->exec("CREATE TABLE IF NOT EXISTS tenant_modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            module_key VARCHAR(50) NOT NULL,
            enabled TINYINT(1) NOT NULL DEFAULT 0,
            enabled_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_tenant_module (tenant_id, module_key),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // --- RBAC-uitbreiding naast de bestaande role-enum-kolom ---
        $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(100) NOT NULL UNIQUE,
            description VARCHAR(255) DEFAULT ''
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role ENUM('owner','admin','user') NOT NULL,
            permission_id INT NOT NULL,
            UNIQUE KEY unique_role_permission (role, permission_id),
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // --- Open API voor partnerintegraties (api/v1) ---
        $pdo->exec("CREATE TABLE IF NOT EXISTS api_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            token_hash VARCHAR(64) NOT NULL,
            last_used_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            revoked_at TIMESTAMP NULL,
            UNIQUE KEY unique_token_hash (token_hash),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // --- Branding / white-labeling (bestaande tenants-tabel uitbreiden) ---
        self::ensurePlanColumnIsVarchar();
        self::ensureColumn('tenants', 'logo_path', "VARCHAR(500) NULL");
        self::ensureColumn('tenants', 'brand_color', "VARCHAR(20) NULL DEFAULT '#2563eb'");
        self::ensureColumn('tenants', 'locale', "VARCHAR(10) NOT NULL DEFAULT 'nl'");
        self::ensureColumn('tenants', 'onboarding_step', "INT NOT NULL DEFAULT 0");

        // --- 2FA (TOTP) op de bestaande users-tabel ---
        self::ensureColumn('users', 'totp_secret', "VARCHAR(64) NULL");
        self::ensureColumn('users', 'totp_enabled', "TINYINT(1) NOT NULL DEFAULT 0");
        self::ensureColumn('users', 'totp_confirmed_at', "TIMESTAMP NULL");

        self::initCrmSchema($pdo);
        self::initProjectenSchema($pdo);
        self::initVoorraadSchema($pdo);

        self::seedPermissions();
        self::seedDemoTenant();
    }

    /**
     * Idempotente demo-tenant zodat de inloggegevens die op de loginpagina staan
     * (demo-bedrijf / admin@demo.nl / demo123) ook echt werken, net als bij de
     * losse demo-apps. Doet niets als de tenant al bestaat.
     */
    private static function seedDemoTenant(): void
    {
        $pdo = self::connect();
        $tenant = self::fetch("SELECT id FROM tenants WHERE slug = 'demo-bedrijf'");
        if ($tenant) {
            return;
        }

        $pdo->exec("INSERT INTO tenants (name, slug, plan) VALUES ('Demo Bedrijf', 'demo-bedrijf', 'business')");
        $tenantId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            "INSERT INTO users (tenant_id, email, password, name, role) VALUES (?, ?, ?, ?, 'owner')"
        );
        $stmt->execute([$tenantId, 'admin@demo.nl', password_hash('demo123', PASSWORD_DEFAULT), 'Demo Beheerder']);

        $pdo->prepare(
            "INSERT INTO subscriptions (tenant_id, plan, status) VALUES (?, 'business', 'active')"
        )->execute([$tenantId]);

        $coreModules = ['facturatie', 'hr', 'contract', 'crm', 'projecten', 'voorraad'];
        $stmt = $pdo->prepare(
            "INSERT INTO tenant_modules (tenant_id, module_key, enabled, enabled_at) VALUES (?, ?, 1, NOW())"
        );
        foreach ($coreModules as $key) {
            $stmt->execute([$tenantId, $key]);
        }
    }

    /** --- CRM-module: leads, offertes, verkooporders --- */
    private static function initCrmSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS crm_leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            company VARCHAR(255),
            email VARCHAR(255),
            phone VARCHAR(50),
            source VARCHAR(100),
            status ENUM('nieuw','gekwalificeerd','offerte','gewonnen','verloren') DEFAULT 'nieuw',
            value DECIMAL(10,2) DEFAULT 0,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS crm_quotes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            lead_id INT NULL,
            number VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            status ENUM('concept','verstuurd','geaccepteerd','afgewezen') DEFAULT 'concept',
            amount DECIMAL(10,2) DEFAULT 0,
            valid_until DATE NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (lead_id) REFERENCES crm_leads(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS crm_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            quote_id INT NULL,
            number VARCHAR(50) NOT NULL,
            customer_name VARCHAR(255) NOT NULL,
            status ENUM('nieuw','in_behandeling','geleverd','gefactureerd','geannuleerd') DEFAULT 'nieuw',
            amount DECIMAL(10,2) DEFAULT 0,
            order_date DATE NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (quote_id) REFERENCES crm_quotes(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /** --- Projecten-module: projecten, taken, urenregistratie --- */
    private static function initProjectenSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS projecten_projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            client_name VARCHAR(255),
            status ENUM('gepland','actief','on_hold','afgerond','geannuleerd') DEFAULT 'gepland',
            start_date DATE NULL,
            end_date DATE NULL,
            budget_hours DECIMAL(8,2) DEFAULT 0,
            budget_amount DECIMAL(10,2) DEFAULT 0,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS projecten_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            assignee_id INT NULL,
            status ENUM('open','bezig','klaar') DEFAULT 'open',
            due_date DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projecten_projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS projecten_time_entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            project_id INT NOT NULL,
            task_id INT NULL,
            user_id INT NOT NULL,
            entry_date DATE NOT NULL,
            hours DECIMAL(5,2) NOT NULL,
            description VARCHAR(500),
            billable TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projecten_projects(id) ON DELETE CASCADE,
            FOREIGN KEY (task_id) REFERENCES projecten_tasks(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /** --- Voorraad-module: magazijnen, producten/voorraad, inkooporders, materieel --- */
    private static function initVoorraadSchema(\PDO $pdo): void
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_warehouses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            location VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            sku VARCHAR(100) NOT NULL,
            name VARCHAR(255) NOT NULL,
            unit VARCHAR(30) DEFAULT 'stuks',
            purchase_price DECIMAL(10,2) DEFAULT 0,
            sales_price DECIMAL(10,2) DEFAULT 0,
            min_stock INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_stock (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            product_id INT NOT NULL,
            warehouse_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 0,
            UNIQUE KEY unique_product_warehouse (product_id, warehouse_id),
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES voorraad_products(id) ON DELETE CASCADE,
            FOREIGN KEY (warehouse_id) REFERENCES voorraad_warehouses(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_purchase_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            number VARCHAR(50) NOT NULL,
            supplier_name VARCHAR(255) NOT NULL,
            status ENUM('concept','besteld','ontvangen','geannuleerd') DEFAULT 'concept',
            order_date DATE NULL,
            expected_date DATE NULL,
            warehouse_id INT NULL,
            total DECIMAL(10,2) DEFAULT 0,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
            FOREIGN KEY (warehouse_id) REFERENCES voorraad_warehouses(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_purchase_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            purchase_order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) DEFAULT 0,
            total DECIMAL(10,2) DEFAULT 0,
            FOREIGN KEY (purchase_order_id) REFERENCES voorraad_purchase_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES voorraad_products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS voorraad_equipment (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(100),
            serial_number VARCHAR(100),
            status ENUM('beschikbaar','in_gebruik','onderhoud','defect') DEFAULT 'beschikbaar',
            assigned_to VARCHAR(255),
            location VARCHAR(255),
            purchase_date DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    /**
     * Idempotente seed van het permissie-catalogus + default role-koppelingen.
     * Draait bij elke request maar voegt alleen ontbrekende rijen toe (INSERT IGNORE).
     */
    private static function seedPermissions(): void
    {
        $pdo = self::connect();

        $permissions = [
            'dashboard.view' => 'Dashboard en KPI\'s bekijken',
            'crm.view' => 'CRM-gegevens bekijken',
            'crm.manage' => 'Leads, offertes en orders beheren',
            'projecten.view' => 'Projecten en uren bekijken',
            'projecten.manage' => 'Projecten en taken beheren',
            'voorraad.view' => 'Voorraad bekijken',
            'voorraad.manage' => 'Voorraad, inkoop en materieel beheren',
            'hr.manage' => 'HR-gegevens beheren',
            'facturatie.manage' => 'Facturen en klanten beheren',
            'beheer.manage' => 'Modules, branding en abonnement beheren',
            'api.manage' => 'API-tokens beheren',
        ];

        foreach ($permissions as $key => $description) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (`key`, description) VALUES (?, ?)");
            $stmt->execute([$key, $description]);
        }

        $rolePermissions = [
            'owner' => array_keys($permissions),
            'admin' => [
                'dashboard.view', 'crm.view', 'crm.manage', 'projecten.view', 'projecten.manage',
                'voorraad.view', 'voorraad.manage', 'hr.manage', 'facturatie.manage',
            ],
            'user' => [
                'dashboard.view', 'crm.view', 'projecten.view', 'voorraad.view',
            ],
        ];

        foreach ($rolePermissions as $role => $keys) {
            foreach ($keys as $key) {
                $pdo->prepare(
                    "INSERT IGNORE INTO role_permissions (role, permission_id)
                     SELECT ?, id FROM permissions WHERE `key` = ?"
                )->execute([$role, $key]);
            }
        }
    }
}
