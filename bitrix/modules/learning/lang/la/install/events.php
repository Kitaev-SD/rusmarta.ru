<?
$MESS["NEW_LEARNING_TEXT_ANSWER_DESC"] = "#ID# - ID del resultado
#ATTEMPT_ID# - ID del intento
#TEST_NAME# - Nombre de la prueba
#USER# - El usuario toma una prueba
#DATE# - La fecha y hora
#QUESTION_TEXT# - La pregunta
#ANSWER_TEXT# - La respuesta
#EMAIL_FROM# - e-mail del remitente
#EMAIL_TO# - e-mail del destinatario
#MESSAGE_TITLE# - Asunto de mensaje del e-mail";
$MESS["NEW_LEARNING_TEXT_ANSWER_MESSAGE"] = "Mensaje desde #SITE_NAME#
 ------------------------------------------
Curso:#COURSE_NAME#
Prueba:#TEST_NAME#

 ------------------------------------------
Curso:#COURSE_NAME#
Prueba:#TEST_NAME#
Usuario: #USER#
Fecha: #DATE:
------------------------------------------

Pregunta:
------------------------------------------
#QUESTION_TEXT#

Respuesta:
------------------------------------------
#ANSWER_TEXT#
------------------------------------------

Para ver y editar la respuesta, siga el siguiente enlace:
http://#SERVER_NAME#/bitrix/admin/learn_test_result_edit.php?lang=en&ID=#ID#&ATTEMPT_ID=#ATTEMPT_ID#.


Este mensaje ha sido generado automáticamente.";
$MESS["NEW_LEARNING_TEXT_ANSWER_NAME"] = "Texto de la Nueva Respuesta";
$MESS["NEW_LEARNING_TEXT_ANSWER_SUBJECT"] = "#SITE_NAME#: #COURSE_NAME#: #MESSAGE_TITLE#";
?>