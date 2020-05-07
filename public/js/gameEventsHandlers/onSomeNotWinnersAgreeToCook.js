const someNotWinnersAgreeToCook = (msgObject) => {
   console.dir(msgObject);
   if (!msgObject.savingPlayers.includes($('#playerName').text())) {

      $('#save').show();
      $('#raise').show();
  }
};

export default someNotWinnersAgreeToCook;