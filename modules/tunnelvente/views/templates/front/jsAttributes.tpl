
<script type="text/javascript">
    
if (typeof combinationImages !== 'undefined' && combinationImages)
{
	combinationImagesJS = [];
	combinationImagesJS[0] = [];
	var k = 0;
    for (var i in combinationImages)
	{
		combinationImagesJS[i] = [];
        for (var j in combinationImages[i])
        {
            var id_image = parseInt(combinationImages[i][j]['id_image']);
         	if (id_image)
            {
				combinationImagesJS[0][k++] = id_image;
				combinationImagesJS[i][j] = [];
				combinationImagesJS[i][j] = id_image;
            }
        }
	}

    if (typeof combinationImagesJS[0] !== 'undefined' && combinationImagesJS[0])
    {
       var array_values = [];
       for (var key in arrayUnique(combinationImagesJS[0]))
           array_values.push(combinationImagesJS[0][key]);
       combinationImagesJS[0] = array_values;
    }
	combinationImages = combinationImagesJS;
}

if (typeof combinations !== 'undefined' && combinations)
{
	combinationsJS = [];
	var k = 0;
	for (var i in combinations)
	{
		globalQuantity += combinations[i]['quantity'];
		combinationsJS[k] = [];
		combinationsJS[k]['idCombination'] = parseInt(i);
		combinationsJS[k]['idsAttributes'] = combinations[i]['attributes'];
		combinationsJS[k]['quantity'] = combinations[i]['quantity'];
		combinationsJS[k]['price'] = combinations[i]['price'];
		combinationsJS[k]['ecotax'] = combinations[i]['ecotax'];
		combinationsJS[k]['image'] = parseInt(combinations[i]['id_image']);
		combinationsJS[k]['reference'] = combinations[i]['reference'];
		combinationsJS[k]['unit_price'] = combinations[i]['unit_impact'];
		combinationsJS[k]['minimal_quantity'] = parseInt(combinations[i]['minimal_quantity']);

		combinationsJS[k]['available_date'] = [];
			combinationsJS[k]['available_date']['date'] = combinations[i]['available_date'];
			combinationsJS[k]['available_date']['date_formatted'] = combinations[i]['date_formatted'];

		combinationsJS[k]['specific_price'] = [];
			combinationsJS[k]['specific_price']['reduction_percent'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction'] && combinations[i]['specific_price']['reduction_type'] == 'percentage') ? combinations[i]['specific_price']['reduction'] * 100 : 0;
			combinationsJS[k]['specific_price']['reduction_price'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction'] && combinations[i]['specific_price']['reduction_type'] == 'amount') ? combinations[i]['specific_price']['reduction'] : 0;
			combinationsJS[k]['price'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['price'] && parseInt(combinations[i]['specific_price']['price']) != -1) ? combinations[i]['specific_price']['price'] :  combinations[i]['price'];

		combinationsJS[k]['reduction_type'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['reduction_type']) ? combinations[i]['specific_price']['reduction_type'] : '';
		combinationsJS[k]['id_product_attribute'] = (combinations[i]['specific_price'] && combinations[i]['specific_price']['id_product_attribute']) ? combinations[i]['specific_price']['id_product_attribute'] : 0;
		k++;                
	}
	combinations = combinationsJS;        
}
</script>