class StationView {
  // Vous pourriez ajouter des méthodes liées à l'affichage ici
  static renderStation(station) {
    return {
      id: station._id,
      activation_date: station.activation_date,
      name: station.name,
      user_id: station.user_id,
      mac_address: station.mac_address,
    };
  }

  static renderStations(stations) {
    return stations.map(this.renderStation);
  }
}

module.exports = StationView;
