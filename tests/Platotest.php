<?php
use PHPUnit\Framework\TestCase;

class PlatoTest extends TestCase {
    private $db;

    protected function setUp(): void {
        // Configuración para pruebas
        $this->configureTestEnvironment();
        
        // Conexión a BD de prueba
        $this->initializeTestDatabase();
        
        // Crear estructura de prueba
        $this->createTestTable();
    }

    private function configureTestEnvironment(): void {
        // Define las rutas según tu estructura de proyecto
        define('ROOT_PATH', realpath(__DIR__.'/../../'));
        define('TEST_CONFIG_PATH', ROOT_PATH.'/tests/config/');
        
        // Cargar configuración de prueba si existe
        if (file_exists(TEST_CONFIG_PATH.'database_test.php')) {
            require_once TEST_CONFIG_PATH.'database_test.php';
        } else {
            $this->markTestSkipped(
                'Archivo de configuración de prueba no encontrado en: '.TEST_CONFIG_PATH
            );
            return;
        }
    }

    private function initializeTestDatabase(): void {
        $this->db = new mysqli(
            DB_TEST_HOST, 
            DB_TEST_USER, 
            DB_TEST_PASS, 
            DB_TEST_NAME
        );

        if ($this->db->connect_error) {
            $this->fail("Error conectando a la BD de prueba: ".$this->db->connect_error);
        }
    }

    private function createTestTable(): void {
        $createTable = "CREATE TABLE IF NOT EXISTS plato (
            idplato INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT NOT NULL,
            precio DECIMAL(10,2) NOT NULL,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$this->db->query($createTable)) {
            $this->fail("Error creando tabla de prueba: ".$this->db->error);
        }
    }

    protected function tearDown(): void {
        if ($this->db) {
            // Limpiar la tabla en lugar de eliminarla para mejor performance
            $this->db->query("TRUNCATE TABLE plato");
            $this->db->close();
        }
    }

    public function testCrearPlatoValido(): void {
        // Datos de prueba
        $testData = [
            'nombre' => 'Pasta Carbonara',
            'descripcion' => 'Pasta con salsa cremosa de huevo, queso y panceta',
            'precio' => '12.99'
        ];

        // Simular POST request
        $_POST = $testData;
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Capturar output
        ob_start();
        include ROOT_PATH.'/admin/platos/crear.php';
        $output = ob_get_clean();

        // Verificar inserción en BD
        $result = $this->db->query("SELECT * FROM plato WHERE nombre = '".$this->db->real_escape_string($testData['nombre'])."'");
        
        $this->assertEquals(1, $result->num_rows, "Debería haber exactamente 1 plato insertado");
        
        $plato = $result->fetch_assoc();
        $this->assertEquals($testData['descripcion'], $plato['descripcion']);
        $this->assertEquals($testData['precio'], $plato['precio']);
    }

    public function testValidacionCamposRequeridos(): void {
        // Simular POST request sin datos requeridos
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        ob_start();
        include ROOT_PATH.'/admin/platos/crear.php';
        $output = ob_get_clean();

        // Verificar que no se insertó nada
        $result = $this->db->query("SELECT COUNT(*) as total FROM plato");
        $row = $result->fetch_assoc();
        $this->assertEquals(0, $row['total'], "No debería insertar registros con datos inválidos");
    }
}