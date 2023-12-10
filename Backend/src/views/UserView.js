class UserView {
  // Vous pourriez ajouter des méthodes liées à l'affichage ici
  static renderUser(user) {
    return {
      id: user._id,
      username: user.username,
      email: user.email,
      inscription_date: user.inscription_date,
      password: user.password,
      firt_name: user.firt_name,
      second_name: user.second_name,
    };
  }

  static renderUsers(users) {
    return users.map(this.renderUser);
  }
}

module.exports = UserView;
