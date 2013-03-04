						<div class="card prices input-validation" id="eCardBodyPrices" >
							<label title="minlength:1" class="line" >
								<strong class="col-5">Preço sugerido</strong>
								<span class="col-6" ><input type="text" value="{{price_suggested}}" name="price_suggested" class="gt8-update input-rounded-shadowed gt8-input-allow-float" /><small class="hidden" >Preço sugerido</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label title="minlength:1" class="line" >
								<strong class="col-5">Preço de custo</strong>
								<span class="col-6" ><input type="text" value="{{price_cost}}" name="price_cost" class="gt8-update input-rounded-shadowed gt8-input-allow-float" /><small class="hidden" >Preço de custo</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label title="minlength:1" class="line" >
								<strong class="col-5">Preço de venda</strong>
								<span class="col-6" ><input type="text" value="{{price_selling}}" name="price_selling" class="gt8-update input-rounded-shadowed gt8-input-allow-float" /><small class="hidden" >Preço de venda</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
							<label title="minlength:1" class="line" >
								<strong class="col-5">Parcelas</strong>
								<span class="col-6" ><input type="text" value="{{price_parts}}" name="price_parts" class="gt8-update input-rounded-shadowed gt8-input-allow-integer" /><small class="hidden" >Parcelas</small></span>
								<em class="col-6">&nbsp;</em>
							</label>
						</div>
						<script type="text/javascript" >//<![CDATA[
							jCube(function(){
								jCube('::input[name=desconto]').getParent('label').appendTo(jCube(':#eCardBodyPrices'));
							});
						//]]></script>
