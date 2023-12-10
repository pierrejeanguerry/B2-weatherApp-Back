const mongoose = require("mongoose");

const userSchema = new mongoose.Schema({
  inscription_date: {
    type: Date,
    required: true,
  },
  username: {
    type: String,
    required: true,
  },
  email: {
    type: String,
    required: true,
    unique: true,
  },
  password: {
    type: String,
    required: true,
    bcrypt: true,
  },
  firt_name: {
    type: String,
    required: true,
  },
  second_name: {
    type: String,
    required: true,
  },
});

const UserModel = mongoose.model("User", userSchema);

module.exports = UserModel;
