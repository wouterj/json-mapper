<?php

// https://gist.github.com/everzet/8a14043d6a63329cee62
function it($m,$p){echo"\033[3",$p?'2m✔︎':'1m✘'.register_shutdown_function(fn()=>die(1))," It $m\033[0m\n";}

require_once dirname(__DIR__, 1).'/vendor/autoload.php';

class Address
{
    public function __construct(private string $street, private int $number)
    {}
}

class User
{
    public readonly string $firstName;
    private string $lastName;
    public readonly Address $address;
    public readonly array $givenNames;
    public readonly array $myCreativityStoppedWorking;
    
    use \WouterJ\JsonMapper\MapFromJson;

    public function familyName(): string
    {
        return $this->lastName;
    }
}

$json = '{"first_name":"Jane","last_name":"Doe","address":{"street":"Somestreet","number":1}}';

it('can map scalar properties',
    'Jane' === User::fromJson('{"first_name":"Jane"}')->firstName
);

it('can map private properties',
    'Doe' === User::fromJson('{"last_name":"Doe"}')->familyName()
);

it('can map nested objects',
    new Address('Somestreet', 1) == User::fromJson('{"address":{"street":"Somestreet","number":1}}')->address
);

it('can map arrays',
    ['Jane', 'Mary'] === User::fromJson('{"given_names":["Jane","Mary"]}')->givenNames
);

it('can map arrays with keys',
    ['some' => 'thing'] === User::fromJson('{"my_creativity_stopped_working":{"some":"thing"}}')->myCreativityStoppedWorking
);
