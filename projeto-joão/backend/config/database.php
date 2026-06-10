<?php
// ====================================================================
// CheckInd — Conexão com o Banco de Dados (Singleton PDO)
// ====================================================================

class Database
{
    private static ?PDO $connection = null;

    /**
     * Retorna a instância única da conexão PDO.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host    = 'localhost';
            $dbname  = 'checkind';
            $user    = 'root';
            $pass    = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Em rotas de API, retornar JSON; nas rotas web, exibir mensagem
                if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Falha na conexão com o banco de dados.']);
                } else {
                    die('<h2>Erro de conexão com o banco de dados.</h2><pre>' . $e->getMessage() . '</pre>');
                }
                exit;
            }
        }

        return self::$connection;
    }
}
