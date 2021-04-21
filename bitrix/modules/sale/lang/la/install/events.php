<?
$MESS["SALE_CHECK_PRINT_ERROR_HTML_SUB_TITLE"] = "�Bienvenido!";
$MESS["SALE_CHECK_PRINT_ERROR_HTML_TEXT"] = "No se puede imprimir el recibo ##CHECK_ID# para el pedido ##ORDER_ACCOUNT_NUMBER# fechado #ORDER_DATE#.

Haga clic aqu� para solucionar el problema:
http://#SERVER_NAME#/bitrix/admin/sale_order_view.php?ID=#ORDER_ID#
";
$MESS["SALE_CHECK_PRINT_ERROR_HTML_TITLE"] = "Error al imprimir recibo";
$MESS["SALE_CHECK_PRINT_ERROR_SUBJECT"] = "Error al imprimir recibo";
$MESS["SALE_CHECK_PRINT_ERROR_TYPE_DESC"] = "#ORDER_ACCOUNT_NUMBER# - id del pedido
#ORDER_DATE# - fecha de la orden
#ORDER_ID# - id de la orden
#CHECK_ID# - id del recibo";
$MESS["SALE_CHECK_PRINT_ERROR_TYPE_NAME"] = "Notificaci�n de error de impresi�n del recibo";
$MESS["SALE_CHECK_PRINT_HTML_SUB_TITLE"] = "Negociaci�n #ORDER_USER#,";
$MESS["SALE_CHECK_PRINT_HTML_TEXT"] = "Su pago se ha procesado y se ha creado un recibo respectivo. Para ver el recibo, utilice el enlace:

#CHECK_LINK#

Para obtener m�s detalles sobre su pedido ##ORDER_ID# or #ORDER_DATE# por favor, siga este enlace: http://#SERVER_NAME#/personal/order/detail/#ORDER_ACCOUNT_NUMBER_ENCODE#/
";
$MESS["SALE_CHECK_PRINT_HTML_TITLE"] = "Su pago por el pedido de #SITE_NAME#";
$MESS["SALE_CHECK_PRINT_SUBJECT"] = "Link del recibo";
$MESS["SALE_CHECK_PRINT_TYPE_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#ORDER_USER# - cliente
#ORDER_ACCOUNT_NUMBER_ENCODE# - Id del pedido para uso en enlaces
#CHECK_LINK# - enlace del recibo";
$MESS["SALE_CHECK_PRINT_TYPE_NAME"] = "Notificaci�n de impresi�n de recibos";
$MESS["SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#ORDER_USER# - Usuario
#EMAIL# - Correo electr�nico del usuario
#BCC# - Correo electr�nico de BCC
#ORDER_LIST# - lista del pedido
#SALE_EMAIL# - correo electr�nico del departamento de ventas";
$MESS["SALE_NEW_ORDER_HTML_SUB_TITLE"] = "Estimado #ORDER_USER#,";
$MESS["SALE_NEW_ORDER_HTML_TEXT"] = "Nosotros hemos recibido su pedido ##ORDER_ID# de #ORDER_DATE#.

Total del pedido: #PRICE#.

Art�culos del pedido:
#ORDER_LIST#

Usted puede hacerle seguimiento a su pedido logue�ndose en su cuenta en #SITE_NAME#. Usted deber� proporcionar su nombre de usuario y contrase�a cuando se autorice en #SITE_NAME#.

Si por alguna raz�n usted requiere cancelar su pedido, use el comando propicio en su cuenta en #SITE_NAME#.

Por favor refi�rase a su n�mero de pedido (##ORDER_ID#) cuando nos contacte.

Gracias por su pedido!
";
$MESS["SALE_NEW_ORDER_HTML_TITLE"] = "Usted ha hecho un pedido con #SITE_NAME#";
$MESS["SALE_NEW_ORDER_MESSAGE"] = "Confirmaci�n del pedido de #SITE_NAME#
------------------------------------------

Esrimado (-a) #ORDER_USER#,  

Su pedido #ORDER_ID# de #ORDER_DATE# ha sido aceptado.  

Valor del pedido: #PRICE#.  

Art�culos del pedido: 
#ORDER_LIST#  

Puede controlar el procesamiento de su pedido (ver el estado actual del pedido) ingresando a la secci�n de su sitio personal en #SITE_NAME#. Tenga en cuenta que necesitar� el nombre de usuario y la contrase�a para ingresar a esta secci�n del sitio en #SITE_NAME#.  

Para cancelar su pedido, utilice la opci�n especial disponible en su secci�n personal en #SITE_NAME#.  

Tenga en cuenta que deber� especificar ID de su pedido:  #ORDER_ID# al solicitar cualquier informaci�n de la administraci�n del sitio web #SITE_NAME#.  

Gracias por su pedido!";
$MESS["SALE_NEW_ORDER_NAME"] = "Nuevo pedido";
$MESS["SALE_NEW_ORDER_RECURRING_DESC"] = "#ORDER_ID# - pedido ID\\r\\n#ORDER_DATE# - fecha del pedido\\r\\n#ORDER_USER# - cliente\\r\\n#PRICE# - monto del pedido\\r\\n#EMAIL# - correo electr�nico del cliente\\r\\n#BCC# - copia oculta direcci�n de correo electr�nico\\r\\n#ORDER_LIST# - contenido del pedido\\r\\n#SALE_EMAIL# - correo electr�nico del depto. de ventas";
$MESS["SALE_NEW_ORDER_RECURRING_MESSAGE"] = "Informaci�n de #SITE_NAME#\\r\\n------------------------------------------\\r\\n\\r\\nEstimado#ORDER_USER#,\\r\\n\\r\\nSu pedido ##ORDER_ID# de #ORDER_DATE# para la renovaci�n de suscripci�n del pedido.\\r\\n\\r\\nMonto del pedido: #PRICE#.\\r\\n\\r\\nArt�culos del pedido:\\r\\n#ORDER_LIST#\\r\\n\\r\\nPuede seguir el estado de su pedido en su �rea privada en #SITE_NAME#. Tenga en cuenta que tendr� que introducir su nombre de usuario y la contrase�a que normalmente utiliza para acceder a #SITE_NAME#.\\r\\n\\r\\nUsted puede Cancelar su pedido en su zona privada en #SITE_NAME#.\\r\\n\\r\\nYusted puede preguntar por su n�mero de pedido#ORDER_ID# en todos los mensajes que env�e #SITE_NAME#.\\r\\n\\r\\nGracias por su Compra!";
$MESS["SALE_NEW_ORDER_RECURRING_NAME"] = "Nuevo pedido Para la Renovaci�n de la Suscripci�n";
$MESS["SALE_NEW_ORDER_RECURRING_SUBJECT"] = "#SITE_NAME#: Nuevo pedido ##ORDER_ID# para renovaci�n de la suscripci�n";
$MESS["SALE_NEW_ORDER_SUBJECT"] = "#SITE_NAME#: Nuevo pedido N#ORDER_ID#";
$MESS["SALE_ORDER_CANCEL_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#EMAIL# - Correo electr�nico del usuario
#ORDER_CANCEL_DESCRIPTION# - Raz�n de cancelaci�n del pedido
#SALE_EMAIL# - correo electr�nico del departamento de ventas";
$MESS["SALE_ORDER_CANCEL_HTML_SUB_TITLE"] = "Pedido ##ORDER_ID# of #ORDER_DATE# se ha cancelado.";
$MESS["SALE_ORDER_CANCEL_HTML_TEXT"] = "#ORDER_CANCEL_DESCRIPTION#

Para ver los detalles de su pedido, por favor haga click ac�: http://#SERVER_NAME#/personal/order/#ORDER_ID#/
";
$MESS["SALE_ORDER_CANCEL_HTML_TITLE"] = "#SITE_NAME#: Pedido cancelado ##ORDER_ID#";
$MESS["SALE_ORDER_CANCEL_MESSAGE"] = "Mensaje informativo desde #SITE_NAME#
------------------------------------------

Pedido ##ORDER_ID# de #ORDER_DATE# se ha cancelado.  

#ORDER_CANCEL_DESCRIPTION#  

#SITE_NAME#";
$MESS["SALE_ORDER_CANCEL_NAME"] = "Cancelar el pedido";
$MESS["SALE_ORDER_CANCEL_SUBJECT"] = "#SITE_NAME#: Pedido N#ORDER_ID# fue cancelado";
$MESS["SALE_ORDER_DELIVERY_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#EMAIL# - Correo electr�nico del usuario
#ORDER_CANCEL_DESCRIPTION# - Raz�n de cancelaci�n del pedido
#SALE_EMAIL# - correo electr�nico del departamento de ventas";
$MESS["SALE_ORDER_DELIVERY_HTML_SUB_TITLE"] = "Pedido ##ORDER_ID# of #ORDER_DATE# hasido enviado.";
$MESS["SALE_ORDER_DELIVERY_HTML_TEXT"] = "Para ver los detalles del pedido, por favor haga clic aqu�: http://#SERVER_NAME#/personal/order/#ORDER_ID#/";
$MESS["SALE_ORDER_DELIVERY_HTML_TITLE"] = "Su pedido #SITE_NAME# ha sido enviado.";
$MESS["SALE_ORDER_DELIVERY_MESSAGE"] = "Mensaje informativo desde #SITE_NAME#
------------------------------------------

Env�o del pedido ##ORDER_ID# de #ORDER_DATE# est� permitido.  

#SITE_NAME#";
$MESS["SALE_ORDER_DELIVERY_NAME"] = "Env�o del pedido habilitado";
$MESS["SALE_ORDER_DELIVERY_SUBJECT"] = "#SITE_NAME#: Env�o del pedido N#ORDER_ID# est� permitido";
$MESS["SALE_ORDER_PAID_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#EMAIL# - Correo electr�nico del usuario
#ORDER_CANCEL_DESCRIPTION# - Raz�n de cancelaci�n del pedido
#SALE_EMAIL# - corre electr�nico del departamento de ventas";
$MESS["SALE_ORDER_PAID_HTML_SUB_TITLE"] = "Su pedido ##ORDER_ID# of #ORDER_DATE# ha sido pagado.";
$MESS["SALE_ORDER_PAID_HTML_TEXT"] = "Para ver los detalles del pedido, por favor haga clic aqu�: http://#SERVER_NAME#/personal/order/#ORDER_ID#/";
$MESS["SALE_ORDER_PAID_HTML_TITLE"] = "El pago para el pedido #SITE_NAME#";
$MESS["SALE_ORDER_PAID_MESSAGE"] = "Mensaje informativo desde  #SITE_NAME#
------------------------------------------

Pedido ##ORDER_ID# de #ORDER_DATE# fue pagado.  

#SITE_NAME#";
$MESS["SALE_ORDER_PAID_NAME"] = "Pedido pagado";
$MESS["SALE_ORDER_PAID_SUBJECT"] = "#SITE_NAME#: Pedido N#ORDER_ID# fue pagado";
$MESS["SALE_ORDER_REMIND_PAYMENT_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#ORDER_USER# - cliente
#PRICE# - monto del pedido
#EMAIL# - correo electr�nico del cliente
#BCC# - correo electr�nico de la copia oculta
#ORDER_LIST# - art�culos del pedido
#SALE_EMAIL# - correo electr�nico del departamento de ventas";
$MESS["SALE_ORDER_REMIND_PAYMENT_HTML_SUB_TITLE"] = "Estimado #ORDER_USER#,";
$MESS["SALE_ORDER_REMIND_PAYMENT_HTML_TEXT"] = "Usted realiz� un pedido de compras ##ORDER_ID# por #PRICE# el #ORDER_DATE#.

Desafortunadamente no hemos recibido su pago a�n.

Usted puede hacerle seguimiento a su pedido logue�ndose en su cuenta en #SITE_NAME#. Usted deber� proporcionar su nombre de usuario y contrase�a cuando se autorice en #SITE_NAME#.

Si por alguna raz�n usted requiere cancelar su pedido, use el comando propicio en su cuenta en #SITE_NAME#.

Por favor refi�rase a su n�mero de pedido (##ORDER_ID#) cuando nos contacte.

Gracias por su pedido!
";
$MESS["SALE_ORDER_REMIND_PAYMENT_HTML_TITLE"] = "No se olvide de pagar su pedido con #SITE_NAME#";
$MESS["SALE_ORDER_REMIND_PAYMENT_MESSAGE"] = "Informaci�n de #SITE_NAME#
------------------------------------------

Estimado #ORDER_USER#,
Usted ha colocado un pedido de ##ORDER_ID# of #ORDER_DATE#, monto: #PRICE#.

Por desgracia, parece que su pago no se ha completado. Ninguno de los fondos se han transferido a nuestra cuenta.

Puede seguir el estado de su pedido en su �rea privada
en#SITE_NAME#. Tenga en cuenta que tendr� que introducir su nombre de usuario
y la contrase�a que normalmente utiliza para acceder a #SITE_NAME#.

Usted puede Cancelar su pedido en su �rea privada en #SITE_NAME#.

Se ruega incluir su n�mero de pedido #ORDER_ID# en todos los mensajes que usted env�e al #SITE_NAME# administraci�n.
Gracias por su compra!";
$MESS["SALE_ORDER_REMIND_PAYMENT_NAME"] = "Recordatorio de Pagos de Pedidos";
$MESS["SALE_ORDER_REMIND_PAYMENT_SUBJECT"] = "#SITE_NAME#: Recordatorio de pagos de pedidos para  ##ORDER_ID#";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_HTML_SUB_TITLE"] = "Estimado #ORDER_USER#,";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_HTML_TEXT"] = "El estado de su env�o para el pedido ##ORDER_NO# de #ORDER_DATE# se ha actualizado a

\"#STATUS_NAME#\" (#STATUS_DESCRIPTION#).

N�mero de seguimiento: #TRACKING_NUMBER#.

Enviado con: #DELIVERY_NAME#.

#DELIVERY_TRACKING_URL##ORDER_DETAIL_URL#";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_HTML_TITLE"] = "Informaci�n de seguimiento de su env�o desde #SITE_NAME# ha sido actualizado";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_SUBJECT"] = "Estado de su env�o desde #SITE_NAME# ha actualizado";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_TYPE_DESC"] = "#SHIPMENT_NO# - ID del env�o
#SHIPMENT_DATE# - enviado el
#ORDER_NO# - # pedido
#ORDER_DATE# - fecha del pedido
#STATUS_NAME# - nombre del estado
#STATUS_DESCRIPTION# - descripci�n de estado
#TRACKING_NUMBER# - el n�mero de rastreo
#EMAIL# - notificar a la direcci�n de correo electr�nico
#BCC# - enviar copia a la direcci�n
#ORDER_USER# - cliente
#DELIVERY_NAME# - nombre del servicio de entrega
#DELIVERY_TRACKING_URL# - sitio web del servicio de entrega para m�s detalles de seguimiento
#ORDER_ACCOUNT_NUMBER_ENCODE# - ID del pedido (para enlaces)
#ORDER_DETAIL_URL# - URL de detalles del pedido";
$MESS["SALE_ORDER_SHIPMENT_STATUS_CHANGED_TYPE_NAME"] = "Actualizaci�n del estado del paquete";
$MESS["SALE_ORDER_TRACKING_NUMBER_HTML_SUB_TITLE"] = "Estimado #ORDER_USER#,";
$MESS["SALE_ORDER_TRACKING_NUMBER_HTML_TEXT"] = "Su pedido #ORDER_ID# del #ORDER_DATE# fue enviado.

El n�mero de seguimiento es: #ORDER_TRACKING_NUMBER#.

Para mayor detalle acerca del pedido, mire: http://#SERVER_NAME#/personal/order/detail/#ORDER_ID#/

E-mail: #SALE_EMAIL#
";
$MESS["SALE_ORDER_TRACKING_NUMBER_HTML_TITLE"] = "El n�mero env�o para su pedido en #SITE_NAME#";
$MESS["SALE_ORDER_TRACKING_NUMBER_MESSAGE"] = "El pedido N #ORDER_ID# del #ORDER_DATE# fue enviado por mail.

El n�mero de seguimiento es: #ORDER_TRACKING_NUMBER#.

Para mayor informaci�n acerca del pedido, vea http://#SERVER_NAME#/personal/order/detail/#ORDER_ID#/

E-mail: #SALE_EMAIL#
";
$MESS["SALE_ORDER_TRACKING_NUMBER_SUBJECT"] = "N�mero de seguimiento para su pedido en #SITE_NAME#";
$MESS["SALE_ORDER_TRACKING_NUMBER_TYPE_DESC"] = "#ORDER_ID# - n�mero ID del pedido
#ORDER_DATE# - fecha del pedido
#ORDER_USER# - cliente
#ORDER_TRACKING_NUMBER# - n�mero de seguimiento del pedido
#EMAIL# - E-Mail del cliente
#BCC# - E-Mail para la copia oculta
#SALE_EMAIL# - E-Mail del departamento de ventas o asociado ";
$MESS["SALE_ORDER_TRACKING_NUMBER_TYPE_NAME"] = "Notificaci�n de cambio en el n�mero de seguimiento";
$MESS["SALE_RECURRING_CANCEL_DESC"] = "#ORDER_ID# - ID del pedido
#ORDER_DATE# - fecha del pedido
#EMAIL# - E-Mail del usuario
#ORDER_CANCEL_DESCRIPTION# - Cancelar descripci�n del pedido
#SALE_EMAIL# - e-mail del departamento de ventas";
$MESS["SALE_RECURRING_CANCEL_MESSAGE"] = "Mensaje informativo desde #SITE_NAME#
------------------------------------------";
$MESS["SALE_RECURRING_CANCEL_NAME"] = "Recurrir a cancelaci�n de pago";
$MESS["SALE_RECURRING_CANCEL_SUBJECT"] = "#SITE_NAME#:recurso de pago fue cancelado";
$MESS["SALE_SUBSCRIBE_PRODUCT_HTML_SUB_TITLE"] = "Estimado #USER_NAME#!";
$MESS["SALE_SUBSCRIBE_PRODUCT_HTML_TEXT"] = "\"#NAME#\" (#PAGE_URL#) es ahora disponible en nuestro stock.

Haga click ac� para hacer el pedido ahora: http://#SERVER_NAME#/personal/cart/

Recuerde autorizarse en el sistema antes de hacer su orden.

Usted recibe esta informaci�n debido a que usted solicit� que lo mantuvi�ramos informado acerca de este producto y/o servicio.

Este mensaje es de generaci�n autom�tica, por favor no responda.

Gracias por comprar con nosotros.!
";
$MESS["SALE_SUBSCRIBE_PRODUCT_HTML_TITLE"] = "Producto vuelve a estar disponible en #SITE_NAME#";
$MESS["SALE_SUBSCRIBE_PRODUCT_SUBJECT"] = "#SITE_NAME#: Producto vuelva a estar disponible";
$MESS["SKGS_STATUS_MAIL_HTML_TITLE"] = "Pedido actualizado en #SITE_NAME#";
$MESS["SMAIL_FOOTER_BR"] = "Saludos cordiales,<br />personal de apoyo.";
$MESS["SMAIL_FOOTER_SHOP"] = "Tienda online";
$MESS["UP_MESSAGE"] = "Mensaje para #SITE_NAME#
------------------------------------------

Estimado #USER_NAME#,

El producto en el que usted est� interesado, \"#NAME#\" (#PAGE_URL#) volver� a estar disponible ahora.
Le recomendamos que realice su pedido (http://#SERVER_NAME#/personal/cart/) tan pronto como sea posible.

Usted est� recibiendo este mensaje porque ha pedido que le informemos cuando este producto se encuentre disponible.

Atentamente,

#SITE_NAME# Servicio al Cliente";
$MESS["UP_SUBJECT"] = "#SITE_NAME#: Producto est� nuevamente en stock";
$MESS["UP_TYPE_SUBJECT"] = "Volver a la notificaci�n de stock";
$MESS["UP_TYPE_SUBJECT_DESC"] = "#USER_NAME# - nombre de usuario
#EMAIL# - e-mail del usuario
#NAME# - nombre del producto
#PAGE_URL# - p�gina de informaci�n del producto";
?>