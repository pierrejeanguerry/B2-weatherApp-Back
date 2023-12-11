import StationModel from "../models/StationModel";
import { Request, Response } from "express";

class StationController {
  async addStation(req: Request, res: Response): Promise<void> {
    const { mac_address } = req.body;

    try {
      const newStation = new StationModel({ mac_address });
      await newStation.save();
      res.status(201).json({ message: "Station added successfully" });
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  // async getStationsByUserId(req: Request, res: Response);
  // async getStationsById(req: Request, res: Response);
}

export default new StationController();
