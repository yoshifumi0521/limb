<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLTableFieldCriteria.class.php');
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

Mock :: generate('lmbDbConnection', 'MockConnection');
Mock :: generate('lmbDbStatement', 'MockStatement');

class lmbSelectRawQueryTest extends UnitTestCase
{
  protected $conn;

  function setUp()
  {
    //this stub uses ' quoting for simpler testing
    $this->conn = new ConnectionTestStub();
  }

  function testSelect()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testNoFields()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddFieldWithFields()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT t3 \n,'t1','t2',t4 FROM test");
  }

  function testNoFieldsAdded()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t4 FROM test");
  }

  function testAddFieldNoFields()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT 't1','t2' FROM test");
  }

  function testAddFieldWithAlias()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1', 'a1');
    $sql->addField('t2', 'a2');

    $this->assertEqual($sql->toString(), "SELECT 't1' as 'a1','t2' as 'a2' FROM test");
  }

  function testNoAddTable()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %tables%', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2','test3'");
  }

  function testAddTableWithAlias()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 't2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2' 't2','test3'");
  }

  function testAddSameTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 'a');
    $sql->addTable('test2', 'b');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2' 'a','test2' 'b'");
  }

  function testAddLeftJoin()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', 'id', 'test', 'article_id');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test LEFT JOIN 'article' ON 'article.id'='test.article_id'");
  }

  function testEmptyCondition()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddCondition()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE \nc1=:c1:");
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nWHERE c1=:c1:");
  }

  function testAddConditionNoHint()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE 1=1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $this->assertEqual($sql->toString(), "SELECT * FROM test WHERE 1=1");
  }

  function testAddSeveralConditions()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE c1=:c1: AND c2=:c2:');
  }

  function testAddConditionToExistingConditions()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n %where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n AND c1=:c1: AND c2=:c2:");
  }

  function testAddConditionToExistingConditionsWithOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tORDER BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tORDER BY t1");
  }

  function testAddConditionToExistingConditionsWithGroup()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tGROUP BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tGROUP BY t1");
  }

  function testEmptyOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY 't1' ASC,'t2' DESC");
  }

  function testAddOrderWithOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY\n 't1' ASC,'t2' DESC");
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY t0 DESC\n ,'t1' ASC,'t2' DESC");
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t0 DESC');
  }

  function testNoGroupsAdded()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testNoGroupsAdded2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0");
  }

  function testAddGroupBy()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %group%', $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY 't1','t2'");
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY \n't1','t2'");
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0 \n,'t1','t2'");
  }

  function testAddHavingNoGroupBy()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));

    try
    {
      $sql->toString();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testAddHaving()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING c1=:c1: AND c2=:c2:");
  }

  function testAddHavingToExistingHaving()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2:");
  }

  function testAddHavingWithExistingOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having% ORDER BY id", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2: ORDER BY id");
  }

  function testGetStatement()
  {
    $conn = new MockConnection();
    $stmt = new MockStatement();

    $conn->expectOnce('newStatement');
    $stmt->expectOnce('set', array('p0t_id', 5));
    $conn->setReturnReference('newStatement', $stmt);

    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $conn);
    $sql->addCriteria(new lmbSQLFieldCriteria('t.id', 5));

    $sql->getStatement();
  }

  function testChaining()
  {
    $sql = new lmbSelectRawQuery($this->conn);
    $string = $sql->from('test')->
              from('test', 'test2')->
              field('foo', 'f')->
              join('test', 'id', 'test2', 'id')->
              order('id', 'desc')->
              group('id')->
              having('id=1')->
              where('id=2')->
              toString();

   $this->assertEqual($string,
                      "SELECT 'foo' as 'f' FROM 'test','test' 'test2' " .
                      "LEFT JOIN 'test' ON 'test.id'='test2.id' " .
                      "WHERE id=2 GROUP BY 'id' HAVING id=1 ORDER BY 'id' desc");
  }

  function testFetchUsingDefaultConnection()
  {
    $sql = new lmbSelectRawQuery('SELECT 1=1');
    $rs = $sql->fetch();
    $this->assertEqual($rs->count(), 1);
    $this->assertEqual($rs[0], true);
  }
}

