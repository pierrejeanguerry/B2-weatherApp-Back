// connect to database
db = connect( 'mongodb://localhost/weatherstation' );


db.user.insertOne(
    {
        first_name: 'John',
        last_name: 'Smith',
        login: 'john.smith',
        password : '0000',
    }
);