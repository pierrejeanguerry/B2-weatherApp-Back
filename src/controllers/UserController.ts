import { UserModel } from "../models/UserModel";
import { Request, Response } from "express";

class UserController {
  async getAllUsers(req: Request, res: Response): Promise<void> {
    try {
      const users = await UserModel.find();
      res.json(users);
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  async addUser(req: Request, res: Response) {
    const { username, email, first_name, second_name, password } = req.body;

    try {
      const newUser = new UserModel({
        username,
        email,
        first_name,
        second_name,
        password,
        inscription_date: new Date(),
      });
      await newUser.save();
      res.status(201).json({ message: "User added successfully" });
    } catch (error) {
      console.log(error);
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  async getUserByEmail(req: Request, res: Response): Promise<any> {
    const userEmail = req.body.email;

    try {
      const user = await UserModel.findOne({ email: userEmail });
      if (!user) {
        return res.status(404).json({ error: "User not found" });
      }
      return user;
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  async updateUser(req: Request, res: Response) {
    const userId = req.params.userId;
    const { username, email } = req.body;

    try {
      const updatedUser = await UserModel.findByIdAndUpdate(
        userId,
        { username, email },
        { new: true }
      );

      if (!updatedUser) {
        return res.status(404).json({ error: "User not found" });
      }

      res.json(updatedUser);
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  async deleteUser(req: Request, res: Response) {
    const userId = req.params.userId;

    try {
      const deletedUser = await UserModel.findByIdAndDelete(userId);

      if (!deletedUser) {
        return res.status(404).json({ error: "User not found" });
      }

      res.json({ message: "User deleted successfully" });
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }
}

export default new UserController();
