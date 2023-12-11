import mongoose from "mongoose";

const stationSchema = new mongoose.Schema({
  activation_date: {
    type: Date,
  },
  name: {
    type: String,
  },
  user_id: {
    type: mongoose.Schema.Types.ObjectId,
    ref: "User",
  },
  mac_address: {
    type: String,
    required: true,
    unique: true,
  },
});

const StationModel = mongoose.model("Station", stationSchema);

export default StationModel;
