<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * JoinVariableDeclaration ::= Join [IndexBy]
 *
 * @author      Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author      Janne Vanhala <jpvanhal@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        http://www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 */
class Doctrine_ORM_Query_Parser_JoinVariableDeclaration extends Doctrine_ORM_Query_ParserRule
{
    protected $_AST = null;
    
    
    public function syntax($paramHolder)
    {
        // JoinVariableDeclaration ::= Join [IndexBy]
        $this->_AST = $this->AST('JoinVariableDeclaration');
        
        $this->_AST->setJoin($this->parse('Join', $paramHolder));

        if ($this->_isNextToken(Doctrine_ORM_Query_Token::T_INDEX)) {
            $this->_AST->setIndexBy($this->parse('IndexBy', $paramHolder));
        }
    }


    public function semantical($paramHolder)
    {
        // If we have an INDEX BY JoinVariableDeclaration
        if ($this->_AST->getIndexby() !== null) {
            // Grab Join component alias
            $joinComponentAlias = $this->_AST->getJoin()
                ->getAliasIdentificationVariable()->getComponentAlias();
                
            // Grab IndexBy component alias
            $indexComponentAlias = $this->_AST->getIndexBy()
                ->getSimpleStateFieldPathExpression()->getIdentificationVariable()->getComponentAlias();

            // Check if we have same component being used in index
            if ($joinComponentAlias !== $indexComponentAlias) {
                $message = "Invalid aliasing. Cannot index by '" . $indexComponentAlias
                         . "' inside '" . $joinComponentAlias . "' scope.";

                $this->_parser->semanticalError($message);
            }
        }

        // Return AST node
        return $this->_AST;
    }
}