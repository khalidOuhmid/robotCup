<?php

function generateEntityTest(string $entityName, array $properties): string
{
    $testCode = "<?php\n\n";
    $testCode .= "namespace App\\Tests\\Unit\\Entity;\n\n";
    $testCode .= "use App\\Entity\\{$entityName};\n";
    $testCode .= "use PHPUnit\\Framework\\TestCase;\n\n";
    $testCode .= "class {$entityName}Test extends TestCase\n{\n";
    $testCode .= "    private {$entityName} \$entity;\n\n";
    $testCode .= "    protected function setUp(): void\n";
    $testCode .= "    {\n";
    $testCode .= "        \$this->entity = new {$entityName}();\n";
    $testCode .= "    }\n\n";

    foreach ($properties as $property => $type) {
        $testCode .= generateTestMethod($property, $type);
    }

    $testCode .= "}\n";
    return $testCode;
}

function generateTestMethod(string $property, string $type): string
{
    $methodName = ucfirst($property);
    $testValue = getTestValue($type);
    
    return "    public function test{$methodName}(): void\n" .
           "    {\n" .
           "        \$this->entity->set{$methodName}({$testValue});\n" .
           "        \$this->assertEquals({$testValue}, \$this->entity->get{$methodName}());\n" .
           "    }\n\n";
}

function getTestValue(string $type): string
{
    switch ($type) {
        case 'int':
            return '42';
        case 'string':
            return "'test_value'";
        case '\DateTimeInterface':
            return 'new \DateTime()';
        case 'Team':
            return '$this->createMock(Team::class)';
        default:
            return 'null';
    }
}

// Exemple d'utilisation pour l'entité Encounter
$properties = [
    'tournament' => 'Tournament',
    'championship' => 'Championship',
    'field' => 'Field',
    'teamBlue' => 'Team',
    'teamGreen' => 'Team',
    'state' => 'string',
    'dateBegin' => '\DateTimeInterface',
    'dateEnd' => '\DateTimeInterface',
    'scoreBlue' => 'int',
    'scoreGreen' => 'int'
];

$testCode = generateEntityTest('Encounter', $properties);
file_put_contents('tests/Unit/Entity/EncounterTest.php', $testCode);
