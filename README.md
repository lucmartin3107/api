# api
Symfony 5.4 e-comerce REST API with some constraints:

In case of errors, the body must be as follows:
{
" error ": " The error message explaining what went wrong ."
}

A user is represented as such:
{
" login ": " foobar " ,
" password ": " mypassword " ,
" email ": " my@email . com " ,
" firstname ": " Foo " ,
" lastname ": " Bar "
}

• registration of user (/api/register)
• connection of user, retrieving the authentication token (/api/login)

The login process, in case of success must return:
{
" token ": " XXXXXXXXXX "
}

This token will then be needed in the “authorization” header of the requests that needs authentication (indicated by the AUTHED flag)
• update current user information (/api/users) – AUTHED
• display current user information (/api/users) – AUTHED

A product is represented as such:
{
" id ": 1 ,
" name ": " Item 3000" ,
" description ": " Best item in the shop !" ,
" photo ": " https :// path / to / image . png " ,
" price ": 13.37
}

• Retrieve list of products (/api/products)
• Retrieve information on a specific product (/api/products/{productId})
• Add a product (api/products) – AUTHED
• Modify and delete a product (/api/products/{productId}) – AUTHED
• Add a product to the shopping cart. (/api/carts/{productId}) – AUTHED
• Remove a product to the shopping cart. (/api/carts/{productId}) – AUTHED
• State of the shopping cart (list of products in the cart). (/api/carts) – AUTHED
• Validation of the cart (aka converting the cart to an order) (/api/carts/validate) – AUTHED

An order is represented as such:
{
" id ": 1 ,
" totalPrice ": 42.01 ,
" creationDate ": "2021 -04 -01 08:32:00 Z " ,
" products ": [
{
" id ": 1 ,
" name ": " Item 3000" ,
" description ": " Best item in the shop !" ,
" photo ": " https :// path / to / image . png " ,
" price ": 13.37
} ,
{
" id ": 2 ,
" name ": " Another item " ,
" description ": " Still good " ,
" photo ": " https :// path / to / image2 . png " ,
" price ": 28.64
}
]
}

• recover all orders of the current user (/api/orders/) – AUTHED
• Get information about a specific order (/api/orders/{orderId}) – AUTHED
• Is only authorized if the order belong to the logged user.

To store the data you need to install a database.
In this project, we will use MariaDB which integrates very well with the symfony framework.
The following environment variable must be used to connect the database:
DATABASE_URL (eg: ‘mysql://db_user:db_password@127.0.0.1:3306/db_name’)
