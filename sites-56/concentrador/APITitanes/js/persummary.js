function generateTreeText(data, level = 0) {
  var treeText = "";

  for (var key in data) {
    if (data.hasOwnProperty(key)) {
      var value = data[key];
      var indentation = "&nbsp;&nbsp;".repeat(level); // Indentation based on the level

      treeText += indentation + key + ": ";

      if (typeof value === "object" && !Array.isArray(value)) {
        treeText += "\n<br>" + generateTreeText(value, level + 1); // Recursively call for objects
      } else if (Array.isArray(value)) {
        treeText +=
          "\n<br>" +
          indentation +
          "  - " +
          value.join("\n" + indentation + "  - ");
      } else {
        treeText += value + "<br>";
      }

      treeText += "\n";
    }
  }

  return treeText;
}

function generarTextoArbol(object) {
  let texto = "";

  // Recorrer el objeto
  for (const key in object) {
    const value = object[key];

    // Si el valor es un objeto, se llama a la función recursivamente
    if (typeof value === "object") {
      texto += `<ul>
        <li class="treeBr"><strong>${key}</strong>
          ${generarTextoArbol(value)}
        </li>
      </ul>`;
    } else {
      texto += `<li class="treeBr"><strong>${key}</strong>: ${value}</li>`;
    }
  }

  return texto;
}

function envia(perId = "") {
  $("#todomas").html(""); 
  esperafn();
  if (perId == "") perId = $("#persona").val();
  $.post(
    "datos.php",
    {
      PersonId: perId,
      func: "personSummary",
    },
    function (data) {
      var datos = eval("(" + data + ")");

      if (datos.error) {
        alert("La consulta a Titanes ha devuelto el error: " + datos.error);
      } else {
        $("#todomas").html(generarTextoArbol(datos.pase.salida.data));
      }

      esperafn();
    }
  );
}

$(document).ready(function () {
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  if (urlParams.get("id")) {
    envia(urlParams.get("id"));
  }

  $.post(
    "datos.php",
    {
      dato: "6",
    },
    function (data) {
      var datos = eval("(" + data + ")");
      var options = $("#persona");
      options.empty();
      $.each(datos, function (index, vale) {
        options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
      });
    }
  );
});
