var mMessage="Campo Requerido";var pPrompt="Error: ";var pDate="Fecha inv�lida";var pAlphanumeric="Ha escrito caract�res que no son v�lidos";var pAlphabetic="Cadena inv�lida";var pInteger="N�mero inv�lido";var pNumber="N�mero inv�lido";var pPhoneNumber="Escriba un n�mero de tel�fono v�lido";var pEmail="Escriba un email v�lido";var pName="S�lo caracteres, n�meros o espacio en blanco";var pBlanco="No se permite esta entrada sin dato.";var pMoney="Debe entrar un valor correspondiente a dinero. \n Revise que s�lo tenga un punto decimal";var pUrl="Usted ha insertado caracteres prohibidos, use s�lo n�meros y letras del alfabeto sin espacios en blanco";
var msg;var defaultEmptyOK=false;var digits="0123456789";var lowercaseLetters="()abcdefghijklmnopqrs&?=%"+String.fromCharCode(13)+String.fromCharCode(10)+String.fromCharCode(34)+"tuvwxyz��������.,-_'` :";var uppercaseLetters="ABCDEFGHIJKLMNOPQRSTUVWXYZ�����½\/�������";var urlcar="-����������������������&\/&%¡²³¤?¼½ ";var whitespace=" \t\n\r";var phoneChars="()+- ";var diasPorMes=new Array(12);diasPorMes[1]=31;diasPorMes[2]=29;diasPorMes[3]=31;diasPorMes[4]=30;diasPorMes[5]=31;diasPorMes[6]=30;diasPorMes[7]=31;diasPorMes[8]=31;diasPorMes[9]=30;diasPorMes[10]=31;diasPorMes[11]=30;diasPorMes[12]=31;function makeArray(n){for(var i=1;i<=n;i++){this[i]=0;}
return this;}
function isEmpty(s){return((s==null)||(s.length==0));}
function isWhitespace(s){var i;if(isEmpty(s))return true;for(i=0;i<s.length;i++){var c=s.charAt(i);if(whitespace.indexOf(c)==-1)return false;}
return true;}
function stripCharsInBag(s,bag){var i;var returnString="";for(i=0;i<s.length;i++){var c=s.charAt(i);if(bag.indexOf(c)==-1)returnString+=c;}
return returnString;}
function stripCharsNotInBag(s,bag){var i;var returnString="";for(i=0;i<s.length;i++){var c=s.charAt(i);if(bag.indexOf(c)!=-1)returnString+=c;}
return returnString;}
function stripWhitespace(s){return stripCharsInBag(s,whitespace);}
function charInString(c,s)
{for(i=0;i<s.length;i++){if(s.charAt(i)==c)return true;}
return false;}
function stripInitialWhitespace(s){var i=0;while((i<s.length)&&charInString(s.charAt(i),whitespace))
i++;return s.substring(i,s.length);}
function isLetter(c){return((uppercaseLetters.indexOf(c)!=-1)||(lowercaseLetters.indexOf(c)!=-1));}
function verUrl(c){return(urlcar.indexOf(c)==-1);}
function isUrl(s){for(i=0;i<s.length;i++)
{var c=s.charAt(i);if(!(verUrl(c)))
return false;}
return true;}
function isDigit(c){return((c>="0")&&(c<="9"));}
function isLetterOrDigit(c){return(isLetter(c)||isDigit(c));}
function isInteger(s){var i;if(isEmpty(s)){if(isInteger.arguments.length==1)return defaultEmptyOK;else return(isInteger.arguments[1]==true);}
for(i=0;i<s.length;i++){var c=s.charAt(i);if(i!=0){if(!isDigit(c))return false;}else{if(!isDigit(c)&&(c!="-")||(c=="+"))return false;}}
return true;}
function isReal(s){var i;if(isEmpty(s)){if(isInteger.arguments.length==1)return defaultEmptyOK;else return(isInteger.arguments[1]==true);}
for(i=0;i<s.length;i++){var c=s.charAt(i);if(!isDigit(c))return false;}
return true;}
function isMoney(s){var i;if(isEmpty(s)){if(isInteger.arguments.length==1)return defaultEmptyOK;else return(isInteger.arguments[1]==true);}
var j=0;for(i=0;i<s.length;i++){var c=s.charAt(i);if(c!='.'){if(!isDigit(c)){return false;}
else{if((j>0)&&(j<2)){j+=1;}
else{if(j!=0)return false;}}}
else j=0;}
return true;}
function isNumber(s){var i;var dotAppeared;dotAppeared=false;if(isEmpty(s)){if(isNumber.arguments.length==1)return defaultEmptyOK;else return(isNumber.arguments[1]==true);}
for(i=0;i<s.length;i++){var c=s.charAt(i);if(i!=0){if(c=="."){if(!dotAppeared)
dotAppeared=true;else
return false;}else
if(!isDigit(c))return false;}else{if(c=="."){if(!dotAppeared)
dotAppeared=true;else
return false;}else
if(!isDigit(c)&&(c!="-")||(c=="+"))return false;}}
return true;}
function isAlphabetic(s){var i;if(isEmpty(s))
if(isAlphabetic.arguments.length==1)return defaultEmptyOK;else return(isAlphabetic.arguments[1]==true);for(i=0;i<s.length;i++)
{var c=s.charAt(i);if(!isLetter(c))
return false;}
return true;}
function isNumStr(s)
{var i;if(isEmpty(s))
if(isNumStr.arguments.length==1)return defaultEmptyOK;else return(isNumStr.arguments[1]==true);for(i=0;i<s.length;i++)
{var c=s.charAt(i);if(!isDigit(c)&&c!=".")
return false;}
return true;}
function isAlphanumeric(s){var i;if(isEmpty(s))
if(isAlphanumeric.arguments.length==1)return defaultEmptyOK;else return(isAlphanumeric.arguments[1]==true);for(i=0;i<s.length;i++)
{var c=s.charAt(i);if(!(isLetter(c)||isDigit(c)))
return false;}
return true;}
function isName(s){if(isEmpty(s))
if(isName.arguments.length==1)return defaultEmptyOK;else return(isAlphanumeric.arguments[1]==true);return(isAlphanumeric(stripCharsInBag(s,whitespace)));}
function isPhoneNumber(s){var modString;if(isEmpty(s)){if(isDateNumber.arguments.length==1)return defaultEmptyOK;else return(isDateNumber.arguments[1]==true);}
modString=stripCharsInBag(s,phoneChars);return(isInteger(modString));}
function isEmail(s)
{if(isEmpty(s)){if(isEmail.arguments.length==1)return defaultEmptyOK;else return(isEmail.arguments[1]==true);}
if(isWhitespace(s))return false;var i=1;var sLength=s.length;while((i<sLength)&&(s.charAt(i)!="@"))
{i++;}
if((i>=sLength)||(s.charAt(i)!="@"))return false;else i+=2;while((i<sLength)&&(s.charAt(i)!="."))
{i++}
if((i>=sLength-1)||(s.charAt(i)!="."))return false;else return true;}
function ValidDayMonth(dayNum,monthNum,yearNum,msg){yearNum=parseInt(yearNum);if((dayNum==0)||(monthNum==0)||(yearNum==0))return alert(msg);if((isNaN(dayNum))||isNaN(monthNum)||isNaN(yearNum))return alert(msg);if((monthNum>12)||(yearNum<1900)||(yearNum>2120))return alert(msg);if((dayNum>diasPorMes[monthNum])||(monthNum==2)&&(dayNum>daysInFeb(yearNum))){return alert(msg);}
else{return true;}}
function NotToday(dayNum,monthNum,yearNum,msg){eldia=new Date();if((dayNum==eldia.getDate())&&((monthNum-1)==eldia.getMonth())&&(yearNum==eldia.getYear())){return alert(msg);}
else return true;}
function daysInFeb(year){return(((year%4==0)&&((!(year%100==0))||(year%400==0)))?29:28);}
function isDate(s)
{dia=s.substring(0,s.indexOf('/'));mes=s.substring(s.indexOf('/')+1,s.lastIndexOf('/'));ano=s.substring(s.lastIndexOf('/')+1,s.length);if(ValidDayMonth(dia,mes,ano,pDate)){return true;}}
function isDatea(s)
{if(!cdate(s)){return alert(msg);}
else return true;}
function statBar(s){window.status=s;}
function warnEmpty(theField)
{if(!theField.readOnly)
theField.focus();if(msg)mMessage=msg;alert(pBlanco);statBar(pBlanco);return false;}
function warnInvalid(theField,s){if(theField.readOnly){theField.focus();}
theField.select();alert(s);statBar(pPrompt+s);return false;}
function checkField(theField,theFunction,emptyOK,s){if(checkField.arguments.length<3)emptyOK=defaultEmptyOK;if(checkField.arguments.length==4){msg=s;}else{if(theFunction==isAlphabetic)msg=pAlphabetic;if(theFunction==isAlphanumeric)msg=pAlphanumeric;if(theFunction==isInteger)msg=pInteger;if(theFunction==isReal)msg=pInteger;if(theFunction==isNumber)msg=pNumber;if(theFunction==isMoney)msg=pMoney;if(theFunction==isEmail)msg=pEmail;if(theFunction==isDate)msg=pDate;if(theFunction==isPhoneNumber)msg=pPhoneNumber;if(theFunction==isName)msg=pName;if(theFunction==isNumStr)msg=pAlphabetic;if(theFunction==isUrl)msg=pUrl;}
if((emptyOK==true)&&(isEmpty(theField.value)))return true;if((emptyOK==false)&&(isEmpty(theField.value)))
return warnEmpty(theField);if(theFunction(theField.value)==true)
return true;else
return warnInvalid(theField,msg);}
function CompareField(field1,field2,operador,msg){var a=field1.value;var b=field2.value;var aint=new Number(a);var bint=new Number(b);if(operador=="igual"){if(a==b)return true; else return warnInvalid(field1,msg);}
if(operador=="notigual"){if(a==b)return warnInvalid(field1,msg);else return true;}
if(operador=="mayor"){if(aint>=bint)return true;else return warnInvalid(field1,msg);}}
function putfocus(place){place.focus();}
function checkLen(s,len){if(len!=s.value.length)warnInvalid(s,"Longitud no valida.\nLa longitud debe ser de "+len+" caracteres");else return true;}
function checkLenHasta(field,len){if(len<field.value.length)warnInvalid(field,"Longitud no valida.\nEste campo debe tener una longitud de hasta "+len+" caracteres");else return true;}
function comparaFecha(fecha1,fecha2){fecha=fecha1.split('/');fechaIni=new Date(fecha[2],fecha[1]-1,fecha[0],00,00,00);fecha=fecha2.split('/');fechaSal=new Date(fecha[2],fecha[1]-1,fecha[0],00,00,00);return((fechaSal-fechaIni)/86400000);}
function comparaFecha2(fecha1,fecha2,men){if(fecha1.value!=''){fecha=fecha1.value.split('/');fechaIni=new Date(fecha[2],fecha[1]-1,fecha[0],00,00,00);fecha=fecha2.value.split('/');fechaSal=new Date(fecha[2],fecha[1]-1,fecha[0],00,00,00);if(fechaIni<fechaSal)return true;else warnInvalid(fecha1,men);}else return true;}
