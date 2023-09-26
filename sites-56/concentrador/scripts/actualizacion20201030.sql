#operaciones que se les cambió el estado de fincimex

update tbl_transacciones set valor=valor_inicial, id_error = '', estado = 'A', tasa = 1, euroEquiv = valor_inicial/tasa/100 where idtransaccion in (201028152120, 201028174365, 201028213086, 201028230673, 201029005182, 201029175162, 201029175536);

update tbl_transacciones set codigo = '936067' where idtransaccion = 201028152120;
update tbl_transacciones set codigo = '612592' where idtransaccion = 201028174365;
update tbl_transacciones set codigo = '686863' where idtransaccion = 201028213086;
update tbl_transacciones set codigo = '487389' where idtransaccion = 201028230673;
update tbl_transacciones set codigo = '682750' where idtransaccion = 201029005182;
update tbl_transacciones set codigo = '964451' where idtransaccion = 201029175162;
update tbl_transacciones set codigo = '964580' where idtransaccion = 201029175536;

update tbl_transacciones set valor=valor_inicial, id_error = '', estado = 'A', tasa = 1.1828, euroEquiv = valor_inicial/tasa/100
where idtransaccion in (201028002867, 201028013675, 201028014311, 201028015451, 201028020937, 201028032516, 201028033840, 201028044369, 201028045053, 201028045666, 201028055067, 201028081898, 201028101358, 201028114833, 201028135453, 201028144272, 201028155350, 201028162624, 201028163873, 201028164010, 201028165943, 201028173362, 201028173419, 201028175995, 201028195893, 201028211028, 201028211256, 201028214471, 201028215088, 201028215729, 201028221962, 201028224812, 201028230665, 201028231795, 201029000729, 201029000936, 201029003482, 201029020982, 201029053654, 201029060156, 201029100030, 201029135684, 201029135944, 201029140151, 201029140750, 201029141136, 201029144086, 201029162458, 201029164455, 201029170328, 201029171528, 201029171838, 201029174397, 201029175963, 201029190261, 201029192549, 201029193910, 201029194724, 201029194867, 201029195390, 201029200531, 201029205940);

update tbl_transacciones set codigo = '013703' where idtransaccion = 201028002867;
update tbl_transacciones set codigo = '048407' where idtransaccion = 201028013675;
update tbl_transacciones set codigo = '045882' where idtransaccion = 201028014311;
update tbl_transacciones set codigo = '121068' where idtransaccion = 201028015451;
update tbl_transacciones set codigo = '535420' where idtransaccion = 201028020937;
update tbl_transacciones set codigo = '186013' where idtransaccion = 201028032516;
update tbl_transacciones set codigo = '007865' where idtransaccion = 201028033840;
update tbl_transacciones set codigo = '278660' where idtransaccion = 201028044369;
update tbl_transacciones set codigo = '282740' where idtransaccion = 201028045053;
update tbl_transacciones set codigo = '065823' where idtransaccion = 201028045666;
update tbl_transacciones set codigo = '068080' where idtransaccion = 201028055067;
update tbl_transacciones set codigo = '830034' where idtransaccion = 201028081898;
update tbl_transacciones set codigo = '075820' where idtransaccion = 201028101358;
update tbl_transacciones set codigo = '032051' where idtransaccion = 201028114833;
update tbl_transacciones set codigo = '041068' where idtransaccion = 201028135453;
update tbl_transacciones set codigo = '044886' where idtransaccion = 201028144272;
update tbl_transacciones set codigo = '94846B' where idtransaccion = 201028155350;
update tbl_transacciones set codigo = '102726' where idtransaccion = 201028162624;
update tbl_transacciones set codigo = '07486F' where idtransaccion = 201028163873;
update tbl_transacciones set codigo = '554971' where idtransaccion = 201028164010;
update tbl_transacciones set codigo = '028689' where idtransaccion = 201028165943;
update tbl_transacciones set codigo = '062450' where idtransaccion = 201028173362;
update tbl_transacciones set codigo = '06137D' where idtransaccion = 201028173419;
update tbl_transacciones set codigo = '077910' where idtransaccion = 201028175995;
update tbl_transacciones set codigo = '005914' where idtransaccion = 201028195893;
update tbl_transacciones set codigo = '069790' where idtransaccion = 201028211028;
update tbl_transacciones set codigo = '02848F' where idtransaccion = 201028211256;
update tbl_transacciones set codigo = '004516' where idtransaccion = 201028214471;
update tbl_transacciones set codigo = '01032Y' where idtransaccion = 201028215088;
update tbl_transacciones set codigo = '02321F' where idtransaccion = 201028215729;
update tbl_transacciones set codigo = '185165' where idtransaccion = 201028221962;
update tbl_transacciones set codigo = '014917' where idtransaccion = 201028224812;
update tbl_transacciones set codigo = '08477D' where idtransaccion = 201028230665;
update tbl_transacciones set codigo = '020904' where idtransaccion = 201028231795;
update tbl_transacciones set codigo = '055097' where idtransaccion = 201029000729;
update tbl_transacciones set codigo = '192141' where idtransaccion = 201029000936;
update tbl_transacciones set codigo = '093544' where idtransaccion = 201029003482;
update tbl_transacciones set codigo = '084356' where idtransaccion = 201029020982;
update tbl_transacciones set codigo = '045530' where idtransaccion = 201029053654;
update tbl_transacciones set codigo = '925411' where idtransaccion = 201029060156;
update tbl_transacciones set codigo = '100151' where idtransaccion = 201029100030;
update tbl_transacciones set codigo = '029441' where idtransaccion = 201029135684;
update tbl_transacciones set codigo = '062258' where idtransaccion = 201029135944;
update tbl_transacciones set codigo = '080209' where idtransaccion = 201029140151;
update tbl_transacciones set codigo = '185433' where idtransaccion = 201029140750;
update tbl_transacciones set codigo = '650950' where idtransaccion = 201029141136;
update tbl_transacciones set codigo = '01045N' where idtransaccion = 201029144086;
update tbl_transacciones set codigo = '948253' where idtransaccion = 201029162458;
update tbl_transacciones set codigo = '157108' where idtransaccion = 201029164455;
update tbl_transacciones set codigo = '618403' where idtransaccion = 201029170328;
update tbl_transacciones set codigo = '441727' where idtransaccion = 201029171528;
update tbl_transacciones set codigo = '642429' where idtransaccion = 201029171838;
update tbl_transacciones set codigo = '007332' where idtransaccion = 201029174397;
update tbl_transacciones set codigo = '041650' where idtransaccion = 201029175963;
update tbl_transacciones set codigo = '020314' where idtransaccion = 201029190261;
update tbl_transacciones set codigo = '042518' where idtransaccion = 201029192549;
update tbl_transacciones set codigo = '094014' where idtransaccion = 201029193910;
update tbl_transacciones set codigo = '021791' where idtransaccion = 201029194724;
update tbl_transacciones set codigo = '569810' where idtransaccion = 201029194867;
update tbl_transacciones set codigo = '02945Z' where idtransaccion = 201029195390;
update tbl_transacciones set codigo = '01597Z' where idtransaccion = 201029200531;
update tbl_transacciones set codigo = '728703' where idtransaccion = 201029205940;

