<center><h1>Resultado da Pesquisa</h1></center>
<?php
 include_once("../conexao.php");
 $pesquisa = $_POST["pesquisa"];

    $listar = mysqli_query($conexao,"select tb_categorias.descricao categoria, tb_jogos.* from tb_jogos
inner join tb_categorias on tb_jogos.id_categoria = tb_categorias.id
where upper(tb_jogos.descricao) like upper('%$pesquisa%')
");
 
if (mysqli_num_rows($listar)==0){
	echo "Nenhum jogo encontrado na sua busca";
	exit();
}

?>
 <table class="table table-striped table-dark">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Categoria</th>
      <th scope="col">Descricao</th>
      <th scope="col">Preço</th>
	  <th scope="col">Imagem</th>
     
    </tr>
  </thead>
  <tbody>
  <?php
  
    while ($registro = mysqli_fetch_array($listar)){
      echo '<tr>
		  <th scope="row">'.$registro["id"].'</th>
		  <td>'.$registro["categoria"].'</td>
		  <td>'.$registro["descricao"].'</td>
		  <td>'.$registro["preco"].'</td>
		  <td><a href="jogos/'.$registro["foto"].'" target="_blank"><img src="jogos/'.$registro["foto"].'" width="100" height="100"></a></td>
		  <td>
		  <input type="hidden" value="'.$registro["id_categoria"].'" id="id_categoria">
		  <input type="hidden" value="'.$registro["foto"].'" id="foto">		
		</tr>';
	   }
?>

</tbody>
</table>