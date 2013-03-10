UPDATE `field` SET `storeRelationAbility` = 'none' WHERE `storeRelationAbility` = '' AND `relation` = "0" AND `satellite` = "0";
UPDATE `field` SET `storeRelationAbility` = 'one' WHERE `storeRelationAbility` = '' AND `relation` IN (2,3,5,7);
UPDATE `field` SET `storeRelationAbility` = 'one' WHERE `storeRelationAbility` = '' AND `alias` IN  ("canStoreRelation","toggle","masterDimensionAlias","rowRequired","dependency","profileId");
UPDATE `field` SET `storeRelationAbility` = 'many' WHERE `storeRelationAbility` = '' AND `alias` = "profileIds";
