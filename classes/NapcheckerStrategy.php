<?php

class NapcheckerStrategy extends BettingStrategy
{
    protected $tableName = "napchecker";

    protected function extractLength(array $classFields)
    {
        $this->length = $classFields[0];
    }

    protected function extractClass(array $classFields)
    {
        $classPieces = array_slice($classFields, 1);
        $this->class = implode(" ", $classPieces);
    }

    protected function extractRunner(string $selection)
    {
        $this->runner = $selection;
    }
}
