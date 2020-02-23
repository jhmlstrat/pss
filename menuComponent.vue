var MenuComponent = {
  template: `
    <div class='container'>
{{ currentComponent() }}
      Team - {{ teamname }} - {{ game}}
      <b-row>
        <b-col cols='10'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <h1><center>{{ team.team_name }} before game {{game }}</center></h1>
      <b-row class='main-menu'>
        <b-col cols='12' class='text-center'>
          <b-button v-show='teamname != "guest"'
                    variant='primary' 
                    size='lg' 
                    v-on:click='switchToRoster();' 
                    v-bind:disabled='!rosterEnabled()'>
            Roster Management
          </b-button>
        </b-col>
      </b-row>
      <b-row class='main-menu'>
        <b-col cols='12' class='text-center'>
          <b-button v-show='teamname != "guest"'
                    variant='primary' 
                    size='lg' 
                    v-on:click='switchToSchedule();'
                    v-bind:disabled='! scheduleEnabled()'>
            Schedule
          </b-button>
        </b-col>
      </b-row>
      <b-row class='main-menu' v-show="nextSeries()">
        <b-col cols='12' class='text-center'>
          <b-button variant='primary' 
                    size='lg' 
                    v-on:click='switchToSchedule();'>
            Next Series
          </b-button>
        </b-col>
      </b-row>
      <b-row class='main-menu' v-show="nextGame()">
        <b-col cols='12' class='text-center'>
          <b-button variant='primary' 
                    size='lg' 
                    v-on:click='switchToSchedule();'>
            Next Game
          </b-button>
        </b-col>
      </b-row>
      <b-row class='main-menu' v-show="resumeGame()">
        <b-col cols='12' class='text-center'>
          <b-button variant='primary' 
                    size='lg' 
                    v-on:click='switchToSchedule();'>
            Resume Game
          </b-button>
        </b-col>
      </b-row>
    </div>
  `,
  data: function() {
    return {
      teamname: 't',
      config: {},
      team: {},
      roster: {},
      schedule: {},
      game: 1,
      betweenSeries: undefined,
      injury: false,
      gameInProgress: false,
      il_length: 0,
    }
  },
  mounted() {
    eBus.$on('configUpdated',(c) => { this.config=c; this.setNames();})
    eBus.$on('teamnameUpdated',(tn) => { this.teamname=tn; this.setNames();});
    eBus.$on('rosterUpdated',(r) => { this.roster=r; this.setGame();});
    eBus.$on('scheduleUpdated',(s) => { this.schedule=s; this.setGame();});
    //eBus.$on('gameUpdated',(g) => { this.game=g; this.setGame();});
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    setNames() {
      if (this.teamname == 't') return;
      if (! this.config.current_year) return;
      for (y of this.config.years) {
        if (y.year == this.config.current_year) {
          this.il_length == y.il_length;
          for (t of y.teams) {
            if (t.team == this.teamname) this.team = t;
          }
        }
      }
    },
    switchToRoster() {
      vue.currentComponent='rosterComponent';
      vue.emitData();
    },
    switchToSchedule() {
      vue.currentComponent='scheduleComponent';
      vue.emitData();
    },
    rosterEnabled() {
      if (JSON.stringify(this.roster) == '{}') return false;
      if (this.gameInProgress === true) return false;
      if (this.betweenSeries === false && this.injury === false) return false;
      return true;
    },
    scheduleEnabled() {
      return JSON.stringify(this.schedule) != '{}';
    },
    nextSeries() {
      if (JSON.stringify(this.schedule) == '{}') return false;
      if (this.gameInProgress === true) return false;
      if (this.betweenSeries === false) return false;
      return true;
    },
    nextGame() {
      if (JSON.stringify(this.schedule) == '{}') return false;
      if (this.gameInProgress === true) return false;
      if (this.betweenSeries === true) return false;
      return true;
    },
    resumeGame() {
      if (JSON.stringify(this.schedule) == '{}') return false;
      if (this.gameInProgress === false) return false;
      return true;
    },
    setGame() {
      this.game = 1;
      this.betweenSeries = true;
      this.gameInProgress = false;
      this.injury = false;
      if (JSON.stringify(this.schedule) == '{}') return;
      if (JSON.stringify(this.roster) == '{}') return;
      //this.schedule.results=[{'home':{'gameNumber':'001'},'final':true,'seriesComplete':false}];
      let testing = false;
      if (testing) {
        r=[];
        gm = {};
        gm.home={};
        gm.home.team='PIT';
        gm.home.gameNumber='001';
        gm.final=true;
        gm.seriesComplete=false;
        r.push(gm);
        this.schedule.results = r;
      }
      day = this.schedule.results.length;
      if (day == 0) return;
      if (this.schedule.results[day-1].home.gameNumber != 'DO') {
        if (this.team.team.toUpperCase() === this.schedule.results[day-1].home.team.toUpperCase()) {
          this.game = parseInt(this.schedule.results[day-1].home.gameNumber);
        } else {
          this.game = parseInt(this.schedule.results[day-1].away.gameNumber);
        }
        if (this.schedule.results[day-1].final == false) {
          this.gameInProgress = true;
        } else {
          this.game = this.game + 1;
          this.betweenSeries = this.schedule.results[day-1].seriesComplete;
        }
      } else {
        if (this.team.team.toUpperCase() === this.schedule.results[day-2].home.team.toUpperCase()) {
          this.game = parseInt(this.schedule.results[day-2].home.gameNumber);
        } else {
          this.game = parseInt(this.schedule.results[day-2].away.gameNumber);
        }
      }
      if (! this.gameInProgress) {
        //console.log(JSON.stringify(this.roster.roster));
        //console.log(this.roster.roster.batters.length);
        //console.log(this.roster.roster.pitchers.length);
        for (let i=0; i< this.roster.roster.batters.length && this.injury == false; i++) {
          //console.log(this.roster.roster.batters[i].rosterItem.moves);
          mt = 'Fm Minors';
          mg = 0;
          for (let j=0; j < this.roster.roster.batters[i].rosterItem.moves.length; j++) {
            if (parseInt(this.roster.roster.batters[i].rosterItem.moves[j].move.gameNumber) <= this.game) {
              mt = this.roster.roster.batters[i].rosterItem.moves[j].move.moveType;
              mg = parseInt(this.roster.roster.batters[i].rosterItem.moves[j].move.gameNumber);
            }
          }
          if (mt == "To minors") continue;
          if (mt == "Traded") continue;
          if (mt == "Traded for") continue;
          //console.log(this.roster.roster.batters[i].rosterItem.injuries);
          //console.log(mt + ' - ' +  this.roster.roster.batters[i].rosterItem.injuries.length);
          if (mt == "On DL") {
            if (day < mg) continue;
            let dg = 0;
            let dig = 0;
            for (let j=mg-1; dg == 0 && j < day; j++) {
              if (this.team.team.toUpperCase() === this.schedule.results[j].home.team.toUpperCase()) {
                dig = parseInt(this.schedule.results[j].home.gameNumber);
              } else {
                dig = parseInt(this.schedule.results[j].away.gameNumber);
              }
              if (dig == mg) dg=j;
            }
            if (dg == 0) continue;
            if ((day - dg) > this.il_length) this.injury = true;
          } else {
            let lastI = 0;
            let dur = 0;
            for (let j=0; j < this.roster.roster.batters[i].rosterItem.injuries.length; j++) {
              if (parseInt(this.roster.roster.batters[i].rosterItem.injuries[j].injury.gameNumber) <= this.game) {
                lastI = parseInt(this.roster.roster.batters[i].rosterItem.injuries[j].injury.gameNumber);
                dur = parseInt(this.roster.roster.batters[i].rosterItem.injuries[j].injury.duration);
              }
            }
            if (lastI == 0) continue;
            if (day < lastI) continue;
            let dg = 0;
            let dig = 0;
            for (let j=lastI-1; dg == 0 && j < day; j++) {
              if (this.team.team.toUpperCase() === this.schedule.results[j].home.team.toUpperCase()) {
                dig = parseInt(this.schedule.results[j].home.gameNumber);
              } else {
                dig = parseInt(this.schedule.results[j].away.gameNumber);
              }
              if (dig == lastI) dg=j;
            }
            if (dg == 0) continue;
            if ((day - dg) <= dur) this.injury = true;
          }
        }
      }
      //console.log(this.game);
      //console.log(this.betweenSeries);
      //console.log(this.gameInProgress);
      //console.log(this.injury);
      if (testing) this.injury = true;
      eBus.$emit('gameUpdated',this.game);
    },
  },
};

