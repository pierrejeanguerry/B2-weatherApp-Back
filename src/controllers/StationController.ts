import StationModel from "../models/StationModel";
import { Request, Response } from "express";

class StationController {
  async getAllStations(req: Request, res: Response): Promise<void> {
    try {
      const stations = await StationModel.find();
      res.json(stations);
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

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

  // async getStationsByUserId(req: Request, res: Response): Promise<Response> {
  //   const user_id = req.params.stationId;

  //   try {
  //     const stations = await StationModel.find({ user_id: user_id });
  //     if (!stations) {
  //       return res.status(404).json({ error: "Stations not found" });
  //     }
  //     res.json(stations);
  //   } catch (error) {
  //     res.status(500).json({ error: "Internal Server Error" });
  //   }
  // }

  // async updateStation(req: Request, res: Response): Promise<void> {
  //   const station_id = req.params.stationId;
  //   const { activation_date, name, user_id } = req.body;

  //   try {
  //     const updatedStation = await StationModel.findByIdAndUpdate(
  //       station_id,
  //       { activation_date, name, user_id },
  //       { new: true }
  //     );

  //     if (!updatedStation) {
  //       return res.status(404).json({ error: "Station not found" });
  //     }

  //     res.json(updatedStation);
  //   } catch (error) {
  //     res.status(500).json({ error: "Internal Server Error" });
  //   }
  // }

  //   async deleteStation(req: Request, res: Response): Promise<void> {
  //     const station_id = req.params.stationId;

  //     try {
  //       const deletedStation = await StationModel.findByIdAndDelete(station_id);

  //       if (!deletedStation) {
  //         return res.status(404).json({ error: "Station not found" });
  //       }

  //       res.json({ message: "Station deleted successfully" });
  //     } catch (error) {
  //       res.status(500).json({ error: "Internal Server Error" });
  //     }
  //   }
}

export default new StationController();
