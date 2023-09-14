<!DOCTYPE html>
<html>

<head>
  <title>Verificador de NCM</title>
  <link rel="stylesheet" href="style2.css">
</head>

<body>
  <h1>Verificador de NCM</h1>

  <form method="post" enctype="multipart/form-data">
    Selecione um arquivo XML
    <input type="file" name="xml_file" accept=".xml">
    <input type="submit" value="Ler XML">
  </form>

  <section>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["xml_file"])) {
      $xml_file = $_FILES["xml_file"]["tmp_name"];
      $tag_alvo = "NCM";

      if (file_exists($xml_file)) {
        $xml = new DOMDocument();
        $xml->load($xml_file);

        $elementos = $xml->getElementsByTagName($tag_alvo);

        if ($elementos->length > 0) {

          $json_file = 'data.json';
          $json_content = file_get_contents($json_file);
          $valores_json = json_decode($json_content, true);


          $ncms_nao_encontrados = [];

          foreach ($elementos as $elemento) {
            $ncm = $elemento->nodeValue;
            $ncm = (string)$ncm;


            $encontrado = false;
            foreach ($valores_json as $valor) {
              if ($valor["Codigo"] == $ncm) {
                $encontrado = true;
                break;
              }
            }

            if (!$encontrado) {
              $ncms_nao_encontrados[] = $ncm;
            }
          }

          if (!empty($ncms_nao_encontrados)) {
            echo "<h3>O {$tag_alvo} abaixo não é válido.</h3>";
            foreach ($ncms_nao_encontrados as $ncm_nao_encontrado) {
              echo "<pre>" . htmlentities($ncm_nao_encontrado) . "</pre>";
            }
          } else {
            echo "<p>Não foi encontrado nenhum NCM inválido no arquivo XML.</p>";
          }
        }
      } else {
        echo "<p>A tag '{$tag_alvo}' não foi encontrada no XML.</p>";
      }
    } else {
      echo "<p>O arquivo XML não foi encontrado.</p>";
    }
    ?>
  </section>

</body>

</html>