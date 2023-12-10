class ReadingView {
  static renderReading(reading) {
    return {
      id: reading._id,
      station_id: reading.station_id,
      date: reading.date,
      temperature: reading.temperature,
      humidity: reading.humidity,
      air_quality_index: reading.air_quality_index,
    };
  }

  static renderReadings(readings) {
    return readings.map(this.renderReading);
  }
}

module.exports = ReadingView;
