const mongoose = require("mongoose");

const readingSchema = new mongoose.Schema({
  station_id: {
    type: mongoose.Schema.Types.ObjectId,
    ref: "Station",
  },
  date: {
    type: Date,
  },
  temperature: {
    type: String,
  },
  humidity: {
    type: String,
  },
  air_quality_index: {
    type: String,
  },
});

const ReadingModel = mongoose.model("Reading", readingSchema);

module.exports = ReadingModel;
