import mongoose, { Model, model } from "mongoose";
import { hash, compare } from "bcrypt";
// const mongoose = require("mongoose");

interface IUser {
  inscription_date: Date;
  email: String;
  first_name: string;
  second_name: string;
  password: string;
  username: string;
}
interface UserMethods {
  isValidPassword: (password: string) => Promise<boolean>;
}

type UserModel = Model<IUser, {}, UserMethods>;

const userSchema = new mongoose.Schema<IUser, UserModel, UserMethods>({
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
  first_name: {
    type: String,
    required: true,
  },
  second_name: {
    type: String,
    required: true,
  },
});

userSchema.pre("save", async function (next) {
  const hashedPassword = await hash(this.password, 10);
  this.password = hashedPassword;

  next();
});

userSchema.method(
  "isValidPassword",
  async function (password: string): Promise<boolean> {
    const isValid = await compare(password, this.password);
    return isValid;
  }
);

export const UserModel = mongoose.model("User", userSchema);
export const User = model<IUser, UserModel>("User", userSchema);
