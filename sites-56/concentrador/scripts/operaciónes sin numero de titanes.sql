select o.idtransaccion 'Orden', o.titOrdenId, (t.valor_inicial/100) 'Importe', case t.moneda when '840' then 'USD' else 'EUR' end 'Moneda',
from_unixtime(t.fecha, '%d/%m/%Y %H:%i') 'Fecha',
concat (c.nombre, ' ', c.papellido, ' ', c.sapellido, ' - ', c.idtitanes) 'Cliente',
concat (b.nombre, ' ', b.papellido, ' ', b.sapellido, ' - ', b.idtitanes) 'Beneficiario', t.estado
from tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b, tbl_transacciones t
where o.idcliente = c.id
and t.estado not in ('D', 'N', 'P')
and t.idtransaccion = o.idtransaccion
and o.idbeneficiario = b.id
and o.idtransaccion between 210501000000 and 210928000000
and (o.titOrdenId is null or o.titOrdenId = '')
