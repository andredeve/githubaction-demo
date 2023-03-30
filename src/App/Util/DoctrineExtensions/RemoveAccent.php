<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 30/01/2019
 * Time: 08:23
 */

namespace App\Util\DoctrineExtensions;


use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class RemoveAccent extends FunctionNode
{
    public $string;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->string = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'remove_accents(' . $this->string->dispatch($sqlWalker) . ')';
    }
}
