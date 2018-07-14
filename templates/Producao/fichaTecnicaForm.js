

	$(document).ready(function() {
			
	    $("#instituicao").select2({	    	  ajax: {
	    	    delay: 500,
	    	    url: function (params) {
					console.log(params);
	    	        return '{{ url('instituicao_findbynome') }}' + '/' + params.term;
	    	    },
	    	    dataType: 'json',
	    	    processResults: function (data, params) {
					var dataNew = $.map(data.results, function (obj) {
						obj.text = obj.nome;
						return obj;
					});
    				return { results: dataNew };
				},
	    	    cache: true
	    	  },
	    	  minimumInputLength: 1
	    	});

	    $('#instituicao').on('select2:select', function (e) {
	        var instituicao = e.params.data;
	        console.log(instituicao);
	        // faz um call no tipoartigo_findbyinstituicaoid e monta o select2
			// pro tipoArtigo
	        
	        $.ajax(
	    	        {
		    	        url: '{{ url('prod_findallbyinstituicao') }}' + '/' + instituicao.id,
		    	        dataType: 'json',
		        		success: function(result){


		        	var dataNew = $.map(result.results, function (obj) {
								obj.text = obj.descricao;
								return obj;
							});
			        		
        		        			$("#tipoArtigo").select2({
        				        		data: dataNew
        				        	});
		        		}
    				}
    				);
	        
	        
	    });


	    
            	
	});
	
	
	
	