const { MongoClient } = require("mongodb");

// Remplacez 'nom-de-la-base-de-donnees' par le nom de votre base de données
const url = "mongodb://localhost:27017/weatherstation";

// Fonction d'utilité pour effectuer des opérations sur la base de données
async function performDatabaseOperations() {
  try {
    const client = await MongoClient.connect(url, {
      useNewUrlParser: true,
      useUnifiedTopology: true,
    });
    console.log("Connexion à MongoDB établie");

    const db = client.db(); // Récupérez la référence à la base de données
    const collection = db.collection("user");

    const document = {
      first_name: "Jeanne",
      last_name: "Smithe",
      login: "jeanne.smithe",
      password: "0001",
    };

    const result = await collection.insertOne(document);
    console.log("Document inséré avec succès", result);

    // Fermez la connexion lorsque vous avez fini
    client.close();
  } catch (err) {
    console.error("Erreur de connexion à MongoDB ou lors de l'insertion", err);
  }
}
// Appelez la fonction pour effectuer des opérations sur la base de données
performDatabaseOperations();
