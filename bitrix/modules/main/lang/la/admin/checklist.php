<?php
$MESS["CL_ADD_COMMENT"] = "Agregar comentario...";
$MESS["CL_ADD_SITES_FIELDS"] = "Agregar m�s";
$MESS["CL_AUTOTEST_DONE"] = "La autocomprobaci�n ha finalizado.";
$MESS["CL_AUTOTEST_RESULT"] = "Resultados de la autocomprobaci�n";
$MESS["CL_AUTOTEST_START"] = "Ejecutar autoprueba";
$MESS["CL_A_STATUS"] = "Aprobado";
$MESS["CL_BACK_TO_CHECKLIST"] = "Regresar a la p�gina anterior";
$MESS["CL_BEGIN"] = "Liberar proyecto";
$MESS["CL_BEGIN_AUTOTEST"] = "Ejecutar autoprueba";
$MESS["CL_BEGIN_PROGECT_TEST"] = "Ver �rbol de pruebas";
$MESS["CL_BEGIN_PROJECT_AUTOTEST"] = "Ejecutar autotest";
$MESS["CL_BEGIN_PROJECT_AUTOTEST_DELAY"] = "Retraso";
$MESS["CL_CANT_CLOSE_PROJECT"] = "No se puede liberar el proyecto. Todas las pruebas <b>obligatorias</b> deben estar en estado de \"Saltados\" o \"Aprobados\". El �rbol de pruebas no debe presentar pruebas fallidas.";
$MESS["CL_CANT_CLOSE_PROJECT_FAILED"] = "Pruebas desaprobadas:";
$MESS["CL_CANT_CLOSE_PROJECT_PASSED_REQUIRE"] = "Las pruebas obligatorias pasado hasta ahora:";
$MESS["CL_CHECK_PROGRESS"] = "Prueba de funcionamiento";
$MESS["CL_CLOSE"] = "Cerrar";
$MESS["CL_DESC"] = "Descripci�n";
$MESS["CL_DONE"] = "Listo";
$MESS["CL_EMPTY_COMMENT"] = "El estatus 'Saltado' requiere un comentario�";
$MESS["CL_EMPTY_DESC"] = "sin descripci�n";
$MESS["CL_END_TEST"] = "Finalizado...";
$MESS["CL_END_TEST_PROCCESS"] = "Finalizando...";
$MESS["CL_FORM_ABOUT_CLIENT_TITLE"] = "Informaci�n de contacto del propietario del proyecto";
$MESS["CL_FROM"] = " del";
$MESS["CL_F_STATUS"] = "Desaprobado";
$MESS["CL_HIDE_COMMENTS"] = "Ocultar comentario";
$MESS["CL_HIDE_REPORT"] = "Ocultar el proyecto";
$MESS["CL_MANUAL"] = "Antes de lanzar el proyecto, usted tendr� que pasar varias pruebas: calidad de desarrollo, par�metros del sistema y conformidad con especificaciones del FrameWork Bitrix. Estas son pruebas <b>obligatorias</b> u <b>opcionales</b>. Para lanzar un proyecto, es suficiente pasar las pruebas obligatorias, sin embargo al lograr aprobar las pruebas opcionales obtendr� mayor cr�dito para su proyecto. Por �ltimo, tenga en cuenta que algunas de las pruebas son evaluadas autom�ticamente.";
$MESS["CL_MANUAL2"] = "<p>La evaluaci�n del proceso de calidad de un proyecto es un proceso de dos pasos: el monitoreo de calidad y encuestas aplicadas al propietario del proyecto. La primera etapa ha sido superada satisfactoriamente.</p>
<p><b>La segunda etapa es el env�o del proyecto a Bitrix24: Encuesta al due�o del proyecto.</b><br>
Por favor especifique la informaci�n de contacto del due�o del proyecto.</p>
<p>Nos pondremos en contacto con �l para hacer diez preguntas cortas relativas a las normas de gesti�n de calidad, la calidad general del proyecto y la facilidad de uso del software de Bitrix.</p>
<p><b>Por favor, h�gale saber que vamos a llamar!</b></p>";
$MESS["CL_MANUAL_MINI"] = "<p>Para enviar el proyecto a Bitrix24 usted debe aprobar las pruebas obligatorias restantes. El estatus de esas pruebas puede ser \"Desaprobado\" (prueba autom�tica desaprobada), o \"Pendiente\" (estas necesitar�n ser revisadas manualmente). Para aprobar pruebas desaprobadas o pendientes, lea las recomendaciones e investigue acerca del error y sus detalles.</p>
<p>En ese punto, usted tiene dos opciones:</p>
<ol>
  <li>resuelva los errores y ejecute nuevamente el test, o</li>
	<li>si usted se inclina a considerar el test como aprobado debido a que hay factores que afectan el resultado del test, usted puede dejar un comentario que ser� leido por uno de nuestros especialistas, luego cambie el estatus del test al estatus aprobado.</li>
</ol>";
$MESS["CL_MANUAL_MINI_2"] = "Estos resultados son suficientes. Ahora puede lanzar el proyecto y guardar los resultados de la prueba.";
$MESS["CL_MANUAL_TEST"] = "<p>La lista de verificaci�n de control de calidad de implementaci�n contiene recomendaciones proporcionadas por Bitrix24 a las que un desarrollador web tiene que adherirse al momento de realizar la integraci�n y despliegue de cualquier proyecto web.</p> 
<p>Estas recomendaciones abarcan todas las etapas de desarrollo, desde el dise�o de la plantilla de la soluci�n web, las pruebas de estr�s, la auditor�a de seguridad, etc. y se basan en las mejores pr�cticas y t�cnicas desarrolladas por Bitrix24 para soluciones web de alta carga. Respete las recomendaciones de esta lista de control para as� lograr un mejor rendimiento, alta seguridad y control de su proyecto web, y reducir los riesgos tecnol�gicos y costos de mantenimiento.</p> 
<p>Las recomendaciones que se recogen en las pruebas que componen este examen se agrupan en dos grupos: obligatorias y opcionales. Las pruebas obligatorias son fundamentalmente cualitativas y cr�ticas por lo tanto deben ser aprobadas de manera obligatoria. Las pruebas opcionales mejorar�n la calidad final del proyecto y el superar estar pruebas es altamente recomendado. Algunas pruebas son autom�ticas. 
<p>El proyecto est� listo para su liberaci�n si los nodos de la lista son de color verde. 
<p>Utilice la siguiente rutina para preparar un proyecto para su liberaci�n. 

<ol><li> El desarrollador comienza autotesting. El sistema ejecuta las pruebas una a una y se marca cada uno de las pruebas autom�ticas como aprobado o no aprobado.</li> <li>El desarrollador revisa cada uno de los autotests no aprobados, corrige los problemas y reinicia el autotest de las pruebas no aprobadas. La prueba pueden ser omitidas de forma manual de ser requerido.</li> <li>El desarrollador revisa cada una de las pruebas manuales y las marca como \"aprobado\" o \"desaprobado\".</li>
<li>El desarrollador deber� proporcionar su informaci�n personal y de la compa��a en el item de lista de verificaci�n correspondiente.</li>
<li>El desarrollador libera el proyecto al cliente. Este �ltimo asegura que todas las pruebas obligatorias se han pasado y no han sido omitidas.</li>
<li>El desarrollador libera el proyecto y lo a�ade al archivo.</li>
</ol>
  <p>Cualquier actualizaci�n posterior importante deber� ser liberada como un proyecto independiente utilizando la lista de control de calidad. Esto asegurar� de la calidad y la robustez del proyecto. 
<p>";
$MESS["CL_MORE_DETAILS"] = "Reporte detallado";
$MESS["CL_MORE_DETAILS_INF"] = "Resultados detallados del autotest ";
$MESS["CL_NEED_TO_STOP"] = "Usted tiene que detener la autocomprobaci�n antes de cambiar el estado.";
$MESS["CL_NEXT_TEST"] = "Siguiente";
$MESS["CL_NOW_AUTOTEST_WORK"] = "�C�mo funciona?";
$MESS["CL_NOW_TO_TEST_IT"] = "�C�mo hacer funcionar la prueba?";
$MESS["CL_NO_COMMENT"] = "Si usted est� dispuesto a considerar el test como aprobado porque hay factores que afectan el resultado de la prueba, puede dejar un comentario para ser le�do por uno de nuestros especialistas y cambiar el estado de la prueba a aprobado.";
$MESS["CL_PASS_TEST"] = "ejecutar autoprueba";
$MESS["CL_PERCENT_LIVE"] = "Finalizado:";
$MESS["CL_PREV_TEST"] = "Anterior";
$MESS["CL_REFRESH_REPORT_STATUSES"] = "Actualizar el estado del proyecto";
$MESS["CL_REPORT_ARCHIVE"] = "Archivar reporte";
$MESS["CL_REPORT_CALL_TIME"] = "Es hora de llamar a";
$MESS["CL_REPORT_CALL_TIME_HINT"] = "especificar cu�ndo un especialista de Bitrix debe llamar a su cliente";
$MESS["CL_REPORT_CITY"] = "Ciudad";
$MESS["CL_REPORT_CLIENT_NAME"] = "Nombre completo del empleado";
$MESS["CL_REPORT_CLIENT_POST"] = "Posici�n de empleado";
$MESS["CL_REPORT_COMMENT"] = "Comentario";
$MESS["CL_REPORT_COMMENT_HELP"] = "Proporcione informaci�n adicional, como las horas de contacto m�s apropiados, etc.";
$MESS["CL_REPORT_COMPANY_NAME"] = "Compa��a";
$MESS["CL_REPORT_DATE"] = "Fecha";
$MESS["CL_REPORT_EMAIL"] = "Correo electr�nico de contacto";
$MESS["CL_REPORT_FIO_TESTER"] = "Nombre completo";
$MESS["CL_REPORT_INFO"] = "Informaci�n del reporte";
$MESS["CL_REPORT_INVITE"] = "Bienvenido al Programa de Evaluaci�n de Calidad. Por favor, complete el formulario y uno de nuestros representantes se pondr� en contacto con usted muy pronto.";
$MESS["CL_REPORT_NOT_FOUND"] = "el reporte no fue encontrado.";
$MESS["CL_REPORT_OLD"] = "El informe est� desactualizado. Realice la prueba de nuevo.";
$MESS["CL_REPORT_PHONE"] = "Tel�fono de contacto";
$MESS["CL_REPORT_PHONE_ADD"] = "ext.";
$MESS["CL_REPORT_SENDED"] = "Se ha enviado el informe a Bitrix24";
$MESS["CL_REPORT_SITES"] = "Sitios web";
$MESS["CL_REPORT_TABLE_CHECKED"] = "Aprobado";
$MESS["CL_REPORT_TABLE_DETAIL"] = "detalles";
$MESS["CL_REPORT_TABLE_FAILED"] = "Desaprobado";
$MESS["CL_REPORT_TABLE_TESTER"] = "Evaluador";
$MESS["CL_REPORT_TABLE_TOTAL"] = "Total de pruebas";
$MESS["CL_REPORT_WARNED"] = "Yo confirmo";
$MESS["CL_REPORT_WARNED2"] = "el cliente ha sido notificado de que un asesor de Bitrix lo llamar�.";
$MESS["CL_REQUIRE_FIELDS"] = "Los campos son obligatorios excepto comentarios y extensi�n telef�nica.";
$MESS["CL_REQUIRE_FIELDS2"] = "Los campos son obligatorios.";
$MESS["CL_REQUIRE_SITES"] = "Los dominios especificados deben aparecer en las preferencias de Sitio web (el campo \"Nombre de dominio\").";
$MESS["CL_RESULT_TEST"] = "Resultado";
$MESS["CL_SAVE_COMMENTS"] = "Guardar comentario";
$MESS["CL_SAVE_REPORT"] = "Guardar reporte";
$MESS["CL_SAVE_SEND_REPORT"] = "Programa de evaluaci�n de calidad";
$MESS["CL_SAVE_SEND_REPORT_CUT"] = "Enviar a Bitrix24";
$MESS["CL_SAVE_SEND_REPORT_HINT"] = "Cuando usted revise su proyecto en el programa de evluaci�n de Calidad de Bitrix24, el sistema s�lo env�a los resultados de las pruebas y los datos de contacto del due�o del proyecto. Si usted desea proporcionar informaci�n adicional, especif�quela como comentarios.";
$MESS["CL_SAVE_SUCCESS"] = "El estatus del test y los comentarios han sido guardados.";
$MESS["CL_SENDING_QC_REPORT"] = "Enviando datos de evaluaci�n de calidad...";
$MESS["CL_SHOW_COMMENTS"] = "Mostrar comentarios";
$MESS["CL_SHOW_HIDDEN"] = "Mostrar datos ocultos";
$MESS["CL_SHOW_REPORT"] = "Iniciar el proyecto";
$MESS["CL_STATUS_COMMENT"] = "Comentar";
$MESS["CL_S_STATUS"] = "Saltar";
$MESS["CL_TAB_DESC"] = "Descripci�n";
$MESS["CL_TAB_RESULT"] = "Resultados";
$MESS["CL_TAB_TEST"] = "Prueba";
$MESS["CL_TEST"] = "Prueba";
$MESS["CL_TESTER"] = "Evaluador";
$MESS["CL_TEST_CHECKED"] = "Aprobado";
$MESS["CL_TEST_CHECKED_COUNT"] = "aprobado";
$MESS["CL_TEST_CHECKED_COUNT_FROM"] = "del";
$MESS["CL_TEST_CHECKED_R"] = "Requerimientos pendientes";
$MESS["CL_TEST_CODE"] = "C�digo de prueba";
$MESS["CL_TEST_FAILED"] = "Desaprobado";
$MESS["CL_TEST_IS_REQUIRE"] = "obligatorio";
$MESS["CL_TEST_LINKS"] = "Links";
$MESS["CL_TEST_NAME"] = "Nombre";
$MESS["CL_TEST_PROGRESS"] = "#check# de #total#";
$MESS["CL_TEST_REQUIRE"] = "Obligatorias";
$MESS["CL_TEST_RESULT"] = "Resultados de la prueba";
$MESS["CL_TEST_SKIP_REQUIRE"] = "Se han saltado pruebas obligatorias";
$MESS["CL_TEST_STATUS"] = "Estado de la prueba";
$MESS["CL_TEST_TOTAL"] = "Total de pruebas";
$MESS["CL_TEST_WAITING"] = "Pendiente";
$MESS["CL_TITLE_CHECKLIST"] = "Control de Calidad de Proyectos";
$MESS["CL_VENDOR"] = "Desarrollador";
$MESS["CL_W_STATUS"] = "Pendiente";
