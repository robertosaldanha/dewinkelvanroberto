<?php

//Função para editar o valor do produto que aparece ao usuário
function priceFormat(float $vlprice)
{
	return number_format($vlprice, 2, ",", ".");
}

?>