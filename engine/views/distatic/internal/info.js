
var familiesInfo	= {
	{{FOREACH:familiesInfo}}
	'{{filename}}': '{{description}}',
	{{/FOREACH}}
	last: null
}
var brandsInfo	= {
	{{FOREACH:brandsTop10}}
	'{{filename}}': '{{description}}',
	{{/FOREACH}}
	last: null
}
