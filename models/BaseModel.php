<?php

abstract class BaseModel
{
    public $con = '';

    function __construct($db)
    {
        $this->con = $db;
    }

    public function batchInsert($table, array $rows, array $columns = array())
    {
        // Is array empty? Nothing to insert!
        if (empty($rows)) {
            return true;
        }

        // Get the column count. Are we inserting all columns or just the specific columns?
        $columnCount = !empty($columns) ? count($columns) : count(reset($rows));

        // Build the column list
        $columnList = !empty($columns) ? '('.implode(', ', $columns).')' : '';

        // Build value placeholders for single row
        $rowPlaceholder = ' ('.implode(', ', array_fill(1, $columnCount, '?')).')';

        // Build the whole prepared query
        $query = sprintf(
            'INSERT INTO %s%s VALUES %s',
            $table,
            $columnList,
            implode(', ', array_fill(1, count($rows), $rowPlaceholder))
        );

        // Prepare PDO statement
        $statement = $this->con->prepare($query);

        // Flatten the value array (we are using ? placeholders)
        $data = array();
        // foreach ($rows as $rowData) {
        //     $data = array_merge($data, array_values($rowData));
        // }
        foreach ($rows as $rowData) {
            foreach ($rowData as $rowField) {
                $data[] = $rowField;
            }
        }

        // Did the insert go successfully?
        return $statement->execute($data);
    }
}