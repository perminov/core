UPDATE `enumset` SET `title`=CONCAT("<span style='color: red'>",`title`,"</span>") WHERE `title` IN ("Выключен","Выключено","Выключена");
UPDATE `enumset` SET `title`=REPLACE(REPLACE(`title`,'<font color="red"', "<span style='color: red'"), '</font>', '</span>') WHERE `title` LIKE '%<font color="red"%' AND `title` LIKE '%</font>%';
