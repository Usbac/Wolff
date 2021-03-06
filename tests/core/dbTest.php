<?php

namespace Tests;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Wolff\Core\DB;
use Wolff\Core\Query;
use Wolff\Exception\InvalidArgumentException;

class DBTest extends TestCase
{

    private $db;


    public function setUp(): void
    {
        global $argv;
        if (!isset($argv[1]) || $argv[1] !== '-db') {
            $this->markTestSkipped('Skipped database test!');
        }

        DB::setCredentials([
            'dsn' => 'sqlite::memory:',
        ]);

        $this->db = new DB();
    }


    public function testInit()
    {
        $this->assertNull($this->db->getAffectedRows());
        $this->db->query('CREATE TABLE customer
            (customer_id INT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL)');
        $this->db->query('CREATE TABLE customer_two
                (customer_id INT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL)');
        $this->db->query("INSERT INTO customer (customer_id, name, email) VALUES
            (1, 'alejandro', 'alejandro@hotmail.com'),
            (2, 'michelle', 'michelle@gmail.com'),
            (3, 'taylor', 'taylor@hotmail.com')");
        $this->assertNull((new DB([ 'dsn' => '' ]))->getPdo());
        $this->assertInstanceOf(Query::class, $this->db->query('SELECT * FROM customer'));
        $this->assertInstanceOf(PDOStatement::class, $this->db->getLastStmt());
        $this->assertInstanceOf(PDO::class, $this->db->getPdo());
        $this->assertEquals('SELECT * FROM customer', $this->db->getLastSql());
        $this->assertEmpty($this->db->getLastArgs());
        $this->assertEquals([
            [
                'customer_id' => 1,
                'name'        => 'alejandro',
                'email'       => 'alejandro@hotmail.com',
            ]
        ], $this->db->query('SELECT * FROM customer WHERE name = ?', 'alejandro')->get());
        $this->assertEquals([ 'alejandro' ], $this->db->getLastArgs());
        $this->assertEquals([
            [
                'customer_id' => 1,
                'name'        => 'alejandro',
                'email'       => 'alejandro@hotmail.com',
            ]
        ], $this->db->runLastSql()->get());
        $this->assertTrue($this->db->tableExists('customer'));
        $this->assertFalse($this->db->tableExists('product'));
        $this->assertTrue($this->db->columnExists('customer', 'name'));
        $this->assertFalse($this->db->columnExists('customer', 'phone'));
        $this->assertFalse($this->db->columnExists('1 == 1', 'phone'));
        $this->assertEquals([
            [
                'customer_id' => 1,
                'name'        => 'alejandro',
                'email'       => 'alejandro@hotmail.com',
            ],
            [
                'customer_id' => 2,
                'name'        => 'michelle',
                'email'       => 'michelle@gmail.com',
            ],
            [
                'customer_id' => 3,
                'name'        => 'taylor',
                'email'       => 'taylor@hotmail.com',
            ],
        ], $this->db->select('customer'));
        $this->assertInstanceOf(Query::class, $this->db->insert('customer',
        [
            'customer_id' => 4,
            'name'        => 'robbie',
            'email'       => 'robbie@williams.com'
        ]));
        $this->assertEquals(4, $this->db->getLastId());
        $this->assertEquals(1, $this->db->getAffectedRows());
        $this->assertEquals([
            [
                'customer_id' => 4,
                'name'        => 'robbie',
                'email'       => 'robbie@williams.com',
            ]
        ], $this->db->select('customer', 'name = ?', 'robbie'));
        $this->assertEquals([
            'robbie@williams.com',
        ], $this->db->select('customer.email', 'name = ?', 'robbie'));
        $this->assertTrue($this->db->delete('customer', 'customer_id > 2'));
        $this->assertEquals(2, $this->db->count('customer', 'customer_id > 0'));
        $this->assertEquals(0, $this->db->count('customer', 'customer_id = -1'));
        $this->assertEquals([
            'customer_id' => 1,
            'name'        => 'alejandro',
            'email'       => 'alejandro@hotmail.com',
        ], $this->db->query('SELECT * FROM customer WHERE name = ?', 'alejandro')->first());
        $this->assertEquals(2, $this->db->query('SELECT * FROM customer')->count());
        $this->assertEquals([
            'alejandro', 'michelle'
        ], $this->db->query('SELECT * FROM customer')->pick('name'));
        $this->assertEquals([
            [
                'customer_id' => 1,
                'name'        => 'alejandro',
            ],
            [
                'customer_id' => 2,
                'name'        => 'michelle',
            ]
        ], $this->db->query('SELECT * FROM customer')->pick('customer_id', 'name'));
        $this->assertEquals([
            [
                'customer_id' => 2,
                'name'        => 'michelle',
                'email'       => 'michelle@gmail.com',
            ]
        ], $this->db->query('SELECT * FROM customer')->limit(1, 2));
        $this->assertEquals('[{"customer_id":"1","name":"alejandro","email":"alejandro@hotmail.com"},{"customer_id":"2","name":"michelle","email":"michelle@gmail.com"}]', $this->db->query('SELECT * FROM customer')->getJson());
        $this->assertTrue($this->db->moveRows('customer', 'customer_two'));
        $this->assertFalse($this->db->moveRows('customer', 'customer_three'));
        //Test proxy to PDO methods
        $this->assertEquals("'text'", $this->db->quote('text'));
        $this->expectException(InvalidArgumentException::class);
        $this->db->insert('customer', [ 1,2,3 ]);
    }

}
