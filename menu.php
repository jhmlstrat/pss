<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="The JHML is a face-to-face Strat-o-Matic league, based in the state of Maryland.">
  <meta name="author" content="">
  <meta name="keywords" content="JHML, Strat-o-Matic, Stratomatic, Maryland">

  <title>JHML</title>
  <link rel="icon" href="favicon.ico">
  <link href="/css/ie10-viewport-bug-workaround.css" rel="stylesheet">   <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <link href="/css/site.css" rel="stylesheet">
  <link href="pss.css" rel="stylesheet">
  <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>
  <link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css"/>
  <script src="https://unpkg.com/vue/dist/vue.js" type="text/javascript"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="https://unpkg.com/babel-polyfill/dist/polyfill.min.js"></script>
  <script src="https://unpkg.com/bootstrap-vue/dist/bootstrap-vue.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.9.0/Sortable.min.js"></script>
<!--
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/15.0.0/vuedraggable.min.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/sortablejs/Sortable.min.js"></script>
  <script src="https://unpkg.com/vue-draggable/lib/vue-draggable.min.js"></script>
  <script src="https://unpkg.com/vue-draggable/polyfills/index.js"></script>
-->
  <script src="https://unpkg.com/lodash/lodash.min.js"></script>
  <script src="menuComponent.vue" type="text/javascript"></script>
  <script src="rosterComponent.vue" type="text/javascript"></script>
  <script src="rotationComponent.vue" type="text/javascript"></script>
  <script src="scheduleComponent.vue" type="text/javascript"></script>
</head>

<body background="../gif/paper1.jpg">
  <div id="myPss">
    <component :is="currentComponent"><component>  
  </div>

  <script>
    const eBus = new Vue();

    var vue = new Vue({
      el: "#myPss",
      data: {
        currentComponent: "menuComponent",
        teamname: '',
        year: undefined,
        config: {},
        cyConfig: {},
        roster: {},
        rotation: {},
        schedule: {},
        team: {},
        game: 0,
        day: 0,
        betweenSeries: true,
        gameInProgress: false,
        injury: false,
        il_length: 0,
        mlb_games: 0,
        roster_size: 0,
        majors: [],
        minors: [],
        il: [],
      },
      components: {
        menuComponent: MenuComponent,
        rosterComponent: RosterComponent,
        scheduleComponent: ScheduleComponent,
        rotationComponent: RotationComponent,
      },
      mounted: function() {
        if ("<?php echo $_GET['teamname']?>" !=  undefined) {
          this.setTeamName("<?php echo $_GET['teamname']?>");
        } else {
          this.setTeamName("guest");
        }
        if ("<?php echo $_GET['year']?>" !=  undefined && "<?php echo $_GET['year']?>" != '') {
          this.year = "<?php echo $_GET['year']?>";
        }
        this.loadConfig();
      },
      watch: {
        year: function() { 
          this.yearChange();
        },
      },
      methods: {
        setTeamName(tn) {
          //console.log(tn);
          this.teamname = tn;
          this.emitData();
        },
        yearChange() {
          if (this.teamname == 'guest') return; 
          if (this.year == undefined) return;
          if (this.config == {} || this.config.years == undefined) return;
          if (this.cyConfig == {} || this.cyConfig.year != this.year) {
            for (y of this.config.years) {
              if (y.year == this.year) {
                this.cyConfig = y;
                this.il_length = y.il_length;
                this.mlb_games = y.mlb_games;
                this.roster_size = y.roster_size;
                for (t of y.teams) {
                  if (t.team == this.teamname) {
                    this.team = t;
                  }
                }
              }
            }
          }
          this.loadRoster(); 
          this.loadSchedule(); 
          this.loadRotation();
        },
        loadConfig() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getConfig.php',headers)
          .then(function (response) {
            self.config = response.data;
            ////console.log(JSON.stringify(self.config));
            if (self.year == undefined) {
              self.year = self.config.current_year;
              //self.emitData();
            } else {
            }
            console.log(self.year);
            self.yearChange();
            //self.loadRoster();
            //self.loadSchedule();
            for (y of self.config.years) {
              if (y.year == self.config.current_year) {
                self.il_length = y.il_length;
                self.mlb_games = y.mlb_games;
                self.roster_size = y.roster_size;
                for (t of y.teams) {
                  if (t.team == self.teamname) {
                    self.team = t;
                  }
                }
              }
            }
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        loadRoster() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getRoster.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.roster = response.data;
            //console.log(JSON.stringify(self.roster));
            self.setGame();
            self.majors = [];
            self.minors = [];
            self.il = [];
            if (self.roster.roster.batters) {
              for (b of self.roster.roster.batters) {
                //console.log(b)
                let lm=self.lastMove(b.rosterItem);
                let mt=lm.lastMove;
                let mg=lm.gameNumber;
                //console.log(lm);
                // Ignore 'Traded'
                if (mt == 'Fm minors' || mt == 'Off DL') {
                  self.majors.push(b.rosterItem);
                } else if (mt == 'To minors' || mt == 'Traded for') {
                  self.minors.push(b.rosterItem);
                } else if (mt == 'On DL') {
                  self.il.push(b.rosterItem);
                }
              }
            }
            if (self.roster.roster.pitchers) {
              for (b of self.roster.roster.pitchers) {
                //console.log(b)
                let lm=self.lastMove(b.rosterItem);
                let mt=lm.lastMove;
                let mg=lm.gameNumber;
                //console.log(lm);
                // Ignore 'Traded'
                if (mt == 'Fm minors' || mt == 'Off DL') {
                  self.majors.push(b.rosterItem);
                } else if (mt == 'To minors' || mt == 'Traded for') {
                  self.minors.push(b.rosterItem);
                } else if (mt == 'On DL') {
                  self.il.push(b.rosterItem);
                }
              }
            }
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        rosterValid() {
          if (JSON.stringify(this.roster) == '{}') return false;
          if (JSON.stringify(this.schedule) == '{}') return false;
          if (this.schedule.results.length > 83 && this.majors.length <= 40) return true;
          if (this.majors.length <= this.roster_size) return true;
          return false;
        },
        loadRotation() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getRotation.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.rotation = response.data;
            //console.log(self.rotation);
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        loadSchedule() {
          var self = this;
          let headers = {headers:{'X-Authorization':'TooManyMLs'}};
          axios.get('/pss/api/getSchedule.php?team='+this.teamname+'&year='+this.year,headers)
          .then(function (response) {
            self.schedule = response.data;
            //console.log(JSON.stringify(self.schedule));
            self.setGame();
            self.emitData();
          })
          .catch(function (error) {
            console.error(error);
          });
        },
        emitData() {
          eBus.$emit('configUpdated',this.config);
          eBus.$emit('teamUpdated',this.team);
          eBus.$emit('rosterUpdated',this.roster);
          eBus.$emit('rotationUpdated',this.rotation);
          eBus.$emit('scheduleUpdated',this.schedule);
        },
        setGame() {
console.log(this.schedule);
          this.game = 1;
          this.betweenSeries = true;
          this.gameInProgress = false;
          this.injury = false;
          if (JSON.stringify(this.schedule) == '{}') return;
          if (JSON.stringify(this.roster) == '{}') return;
          let testing = false;
          if (testing) {
            let r=[];
            let gm = {};
            gm.home={};
            gm.home.team='PIT';
            gm.home.gameNumber='001';
            gm.final=true;
            gm.seriesComplete=false;
            r.push(gm);
            this.schedule.results = r;
          }
          this.day = this.schedule.results.length;
          if (this.day == 0) {this.day ++; return;}
          if (this.schedule.results[this.day-1].home.gameNumber != 'DO') {
            if (this.team.team.toUpperCase() === this.schedule.results[this.day-1].home.team.toUpperCase()) {
              this.game = parseInt(this.schedule.results[this.day-1].home.gameNumber);
            } else {
              this.game = parseInt(this.schedule.results[this.day-1].away.gameNumber);
            }
            if (this.schedule.results[this.day-1].final == false) {
              this.gameInProgress = true;
            } else {
              this.game = this.game + 1;
              this.betweenSeries = this.schedule.results[this.day-1].seriesComplete;
            }
          } else {
            if (this.team.team.toUpperCase() === this.schedule.results[this.day-2].home.team.toUpperCase()) {
              this.game = parseInt(this.schedule.results[this.day-2].home.gameNumber);
            } else {
              this.game = parseInt(this.schedule.results[this.day-2].away.gameNumber);
            }
          }
          this.day ++;
          if (! this.gameInProgress) {
            for (let i=0; i< this.roster.roster.batters.length && this.injury == false; i++) {
              lm=this.lastMove(this.roster.roster.batters[i].rosterItem);
              mt=lm.lastMove;
              mg=lm.gameNumber;
              //console.log(mg);
              if (mt == "To minors") continue;
              if (mt == "Traded") continue;
              if (mt == "Traded for") continue;
              if (mt == "On DL") {
                if (this.day < mg) continue;
                let dg = 0;
                let dig = 0;
                for (let j=mg-1; dg == 0 && j < this.day; j++) {
                  if (this.team.team.toUpperCase() === this.schedule.results[j].home.team.toUpperCase()) {
                    dig = parseInt(this.schedule.results[j].home.gameNumber);
                  } else {
                    dig = parseInt(this.schedule.results[j].away.gameNumber);
                  }
                  if (dig == mg) dg=j;
                }
                if (dg == 0) continue;
                if ((this.day - dg) > this.il_length) this.injury = true;
              } else {
                li=lastInjury(this.roster.roster.batters[i].rosterItem);
                lastI=li.lastInjury;
                dur=li.duration;
                if (lastI == 0) continue;
                if ((this.day - lastI) <= dur) this.injury = true;
              }
            }
          }
          //console.log(this.game);
          //console.log(this.betweenSeries);
          //console.log(this.gameInProgress);
          //console.log(this.injury);
          //if (testing) this.injury = true;
          eBus.$emit('gameUpdated',this.game);
        },
        lastMove(ri) {
          mt = 'Fm minors';
          mg = 0;
          for (let j=0; j < ri.moves.length; j++) {
            if (parseInt(ri.moves[j].move.gameNumber) <= this.game) {
              mt = ri.moves[j].move.moveType;
              mg = parseInt(ri.moves[j].move.gameNumber);
            }
          }
          return {"lastMove":mt,"gameNumber":mg};
        },
        lastInjury(ri) {
          let lastI = 0;
          let dur = 0;
          for (let j=0; j < ri.injuries.length; j++) {
            if (parseInt(ri.injuries[j].injury.gameNumber) <= this.game) {
              lastI = parseInt(ri.injuries[j].injury.gameNumber);
              dur = parseInt(ri.injuries[j].injury.duration);
            }
          }
          return {"lastInjury":this.dayGame(lastI),"duration":dur};
        },
        dayGame(gameNumber) {
          let gd=0;
          if (gameNumber > 0 && this.day >= gameNumber) {
            for (let i=gameNumber; gd == 0 && i < this.schedule.results.length; i++) {
              if (this.schedule.results[i].home.gameNumber == 'DO') continue;
              let g = 0;
              if (this.team.team.toUpperCase() === this.schedule.results[i].home.team.toUpperCase()) {
                g = parseInt(this.schedule.results[i].home.gameNumber);
              } else {
                g = parseInt(this.schedule.results[i].away.gameNumber);
              }
              if (g == gameNumber) gd=i+1;
            }
          }
          return gd;
        },
        seriesGame(gameNumber) {
          let series = 0;
          let gameInSeries = 0;
          let lastGame = 0;
          let found = false;
          let lastOpp = '';
          let lastHA = '';
          for (let i=0; !found && i < this.schedule.results.length; i++) {
            if (this.schedule.results[i].home.gameNumber == 'DO') continue;
            let g = 0;
            let currOpp = '';
            let currHA = '';
            if (this.team.team.toUpperCase() === this.schedule.results[i].home.team.toUpperCase()) {
              g = parseInt(this.schedule.results[i].home.gameNumber);
              currOpp = this.schedule.results[i].away.team.toUpperCase();
              currHA = 'home';
            } else {
              g = parseInt(this.schedule.results[i].away.gameNumber);
              currOpp = this.schedule.results[i].home.team.toUpperCase();
              currHA = 'away';
            }
            if (g == gameNumber) {
              found = true;
            } else {
              if (lastOpp != currOpp || lastHA != currHA || gameInSeries == 4) {
                series ++;
                gameInSeries = 1;
              } else {
                gameInSeries ++;
              }
            }
          }
          if (! found && (lastGame + 1) == gameNumber) {
            if (this.betweenSeries) {
              series ++;
              gameInSeries = 1;
            } else {
              gameInSeries ++;
            }
          }
          return {"series":series,"gameInSeries":gameInSeries};
        },
      },
    });

  </script>
</body>
</html>
