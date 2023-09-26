function imprimirArbol(array, indentacion = 0) {
    // Recorrer cada elemento del array
    array.forEach(function (elemento) {
      // Agregar la indentación correspondiente al nivel actual
      var espacios = ' '.repeat(indentacion);
      console.log(espacios + elemento);
      
      // Si el elemento es un array, imprimirlo como un subárbol
      if (Array.isArray(elemento)) {
        imprimirArbol(elemento, indentacion + 4); // Aumentar la indentación para los subárboles
      }
    });
  }
  
  // Ejemplo de uso
//   var miArray = {"nombre":"Julio","Accounts":[{"Id":222058,"PersonId":225068,"BankInternationalCode":null,"AccountNumber":"ES3068450002680010000032","CurrencyId":1,"CurrencyName":"EUR","Balance":0,"PendingInBalance":0,"PendingOutBalance":0,"BusinessAccount":0,"Alias":"la cuenta del trabajo","Active":true}]};
var cuenta=Array
cuenta["id"]=222058;
cuenta['PersonId']=225068;
cuenta['BankInternationalCode']=null;
cuenta['AccountNumber']='ES3068450002680010000032';
cuenta['CurrencyId']=1;
cuenta['CurrencyName']="EUR";
cuenta['Balance']=0;
cuenta['PendingInBalance']=0;
cuenta['PendingOutBalance']=0;
var miArray=Array
  miArray["nombre"] = 'Julio'
  miArray["Account"]=cuenta
  imprimirArbol(miArray);