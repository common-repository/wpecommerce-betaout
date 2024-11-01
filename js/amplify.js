
  var _bout = _bout || [];
  var _boutAKEY = user_data.betaoutAPI, _boutPID = user_data.betaoutPID; 
  var d = document, f = d.getElementsByTagName("script")[0], _sc = d.createElement("script"); _sc.type = "text/javascript"; _sc.async = true; _sc.src = "//d22vyp49cxb9py.cloudfront.net/jal-v2.min.js"; f.parentNode.insertBefore(_sc, f);
 _bout.push(["identify", {"email" : user_data.email , "customer_id" : user_data.customer_id , "phone" : user_data.phone},{productId:user_data.p_id,categoryId:user_data.cat_id,brandId:""} ]);
