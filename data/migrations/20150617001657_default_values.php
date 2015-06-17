<?php

use Phinx\Migration\AbstractMigration;

class DefaultValues extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO groups(id, name) VALUES(1, \'admin\')');
        $this->execute('INSERT INTO groups(id, name) VALUES(2, \'representante\')');
        $username = 'admin@forcadevendas.com.br';
        $password = sha1('f0rc4d3v3nd4s');
        $sql = sprintf('INSERT INTO users(id, email, password, group_id) VALUES(1, \'%s\', \'%s\', 1);', $username, $password);
        $this->execute($sql);

        $this->execute('ALTER TABLE users ADD UNIQUE unique_email(email)');
    }

    public function down()
    {
        $this->execute('DELETE FROM users WHERE id = 1');
        $this->execute('ALTER TABLE users DROP INDEX unique_email');
        $this->execute('DELETE FROM groups WHERE id = IN(1,2)');
    }
}
