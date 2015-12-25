<?php
require_once('bootstrap.php');

$oParser = new Sabberworm\CSS\Parser(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/core/library/extjs4/resources/css/ext-neptune1.css'));

$oCss = $oParser->parse();
$sMyId = '.extjs';
foreach($oCss->getAllDeclarationBlocks() as $oBlock) {
    foreach($oBlock->getSelectors() as $oSelector) {
        //Loop over all selector parts (the comma-separated strings in a selector) and prepend the id
        $oSelector->setSelector($sMyId . ' ' . $oSelector->getSelector());
    }
}

echo '#### Structure (`var_dump()`)'."\n";
//var_dump($oCss);

echo '#### Output (`render()`)'."\n";
print $oCss->render();
echo "\n";

