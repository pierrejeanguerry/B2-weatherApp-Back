import ReadingModel from "../models/ReadingModel";
import { Request, Response } from "express";

class ReadingController {
  async getAllReadings(req: Request, res: Response): Promise<void> {
    try {
      const readings = await ReadingModel.find();
      res.json(readings);
    } catch (error) {
      res.status(500).json({ error: "Internal Server Error" });
    }
  }

  // async addReading(req: Request, res: Response): Promise<void> {
  //   const { mac_address } = req.body;

  //   try {
  //     const newReading = new ReadingModel({ mac_address });
  //     await newReading.save();
  //     res.status(201).json({ message: "Reading added successfully" });
  //   } catch (error) {
  //     res.status(500).json({ error: "Internal Server Error" });
  //   }
  // }

  // async getReadingsByStationId(req: Request, res: Response): Promise<void> {
  //   const station_id = req.params.readingId;

  //   try {
  //     const readings = await ReadingModel.find({ station_id: station_id });
  //     if (!readings) {
  //       return res.status(404).json({ error: "Readings not found" });
  //     }
  //     res.json(readings);
  //   } catch (error) {
  //     res.status(500).json({ error: "Internal Server Error" });
  //   }
  // }

  // async deleteReading(req: Request, res: Response): Promise<void> {
  //   const reading_id = req.params.readingId;

  //   try {
  //     const deletedReading = await ReadingModel.findByIdAndDelete(reading_id);

  //     if (!deletedReading) {
  //       return res.status(404).json({ error: "Reading not found" });
  //     }

  //     res.json({ message: "Reading deleted successfully" });
  //   } catch (error) {
  //     res.status(500).json({ error: "Internal Server Error" });
  //   }
  // }
}

export default new ReadingController();
