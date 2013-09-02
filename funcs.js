$(document).ready(function(){

	$(".btn").bind('click', function(e){
		var id = e.target.id;
		var condid = 'condition-'+id.replace('link-','');
		id = 'sql-'+id.replace('link-','');
		
		var condition = ($('#'+condid).length > 0)? $('#'+condid).text() : '';
		var sql = $('#'+id).text();

		var tmpcaption = e.target.innerHTML;
		e.target.innerHTML = 'Working...';
		e.target.className = 'btn disabled';


		$.get('./sqlexec.php', {condition: encodeURI(condition), sql: encodeURI(sql)}).done(function(data) {
			e.target.innerHTML = (data != '')? data : 'Done!';
			var extraclass = (data != '')? 'red-text' : 'green-text'; 
			e.target.className = 'btn '+extraclass;		  
		});

	})

})