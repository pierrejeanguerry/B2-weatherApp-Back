const express = require("express");
const bodyParser = require("body-parser");
const mongoose = require("mongoose");
const readingsRoutes = require("./src/routes/taskRoutes");

const app = express();
const PORT = 3000;

// Connexion à la base de données MongoDB
mongoose.connect("mongodb://localhost:27017/taskdb", {
  useNewUrlParser: true,
  useUnifiedTopology: true,
});

app.use(bodyParser.json());

// Differentes routes ici
app.use("/api/station", stationRoutes);

app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
