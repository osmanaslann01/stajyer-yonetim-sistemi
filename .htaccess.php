RewriteEngine On

# public klasörüne yönlendir
RewriteRule ^$ public/ [L]

# public klasörü hariç tüm istekleri public'e yönlendir
RewriteRule (.*) public/$1 [L]