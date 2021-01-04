var ScheduleComponent = {
  name: 'ScheduleComponent',
  template: `
    <div class='container'>
{{ currentComponent() }}
      <b-row>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='#' v-on:click='switchToMenu();'>
            Pss Menu
          </b-button>
        </b-col>
        <b-col cols='8'>
        </b-col>
        <b-col cols='2' class='text-center'>
          <b-button variant='link' size='lg' href='/jhmlhome.html'>
            JHML Home
          </b-button>
        </b-col>
      </b-row>
      <h1><center>{{ year() }} {{ team.city }} {{ team.team_name }} Schedule</center></h1>
      <div class='alert alert-danger text-center' role='alert' v-show='! rosterValid()'>Your roster is invalid - You may not start a series</div>
      <b-row class='pb-3'>
        <b-col cols='6' class='text-center'>
          <strong>Home (51)</strong>
        </b-col>
        <b-col cols='6' class='text-center'>
          <strong>Away (51)</strong>
        </b-col>
      </b-row>
      <b-row v-for="n in spring" class='pb-1' v-bind:key='"S"+n'>
        <b-col cols='6'>
          <!-- {{ schedule.home[n-1] }} -->
          <span class='text-monospace pl-5'>
            <span v-html="teamName(schedule.home[n-1].scheduleItem.awayTeam)"></span>
            ({{ schedule.home[n-1].scheduleItem.numberOfGames }})
            <span v-html="results(schedule.home[n-1].scheduleItem,'h')"></span>
            <b-button variant='primary' size='sm' href='#' v-on:click="doStartSeries(schedule.home[n-1].scheduleItem)" v-show="startSeries(schedule.home[n-1].scheduleItem,'h')" v-bind:disable="fetching">Start Series</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doNextGame(schedule.home[n-1].scheduleItem)" v-show="nextGame(schedule.home[n-1].scheduleItem,'h')">Next Game</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doResumeGame(schedule.home[n-1].scheduleItem)" v-show="resumeGame(schedule.home[n-1].scheduleItem,'h')">Resume Game</b-button>
          </span>
        </b-col>
        <b-col cols='6'>
          <!-- {{ schedule.away[n-1] }} -->
          <span class='text-monospace pl-5'>
            <span v-html="teamName(schedule.away[n-1].scheduleItem.homeTeam)"></span>
            ({{ schedule.away[n-1].scheduleItem.numberOfGames }})
            <span v-html="results(schedule.away[n-1].scheduleItem,'a')"></span>
            <b-button variant='primary' size='sm' href='#' v-on:click="doStartSeries(schedule.away[n-1].scheduleItem)" v-show="startSeries(schedule.away[n-1].scheduleItem,'a')">Start Series</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doNextGame(schedule.away[n-1].scheduleItem)" v-show="nextGame(schedule.away[n-1].scheduleItem,'a')">Next Game</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doResumeGame(schedule.away[n-1].scheduleItem)" v-show="resumeGame(schedule.away[n-1].scheduleItem,'a')">Resume Game</b-button>
          </span>
        </b-col>
      </b-row>
      <b-row class='pt-3 pb-3'>
        <b-col cols='12' class='text-center'>
          <strong>SEPTEMBER</strong>
        </b-col>
      </b-row>
      <b-row v-for="n in fall" class='pb-1' v-bind:key='"F"+n'>
        <b-col cols='6'>
          <!-- {{ schedule.home[spring+n-1] }} -->
          <span class='text-monospace pl-5'>
            <span v-html="teamName(schedule.home[spring+n-1].scheduleItem.awayTeam)"></span>
            ({{ schedule.home[spring+n-1].scheduleItem.numberOfGames }})
            <span v-html="results(schedule.home[spring+n-1].scheduleItem,'h')"></span>
            <b-button variant='primary' size='sm' href='#' v-on:click="doStartSeries(schedule.home[n-1].scheduleItem)" v-show="startSeries(schedule.home[spring+n-1].scheduleItem,'h')">Start Series</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doNextGame(schedule.home[n-1].scheduleItem)" v-show="nextGame(schedule.home[spring+n-1].scheduleItem,'h')">Next Game</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doResumeGame(schedule.home[n-1].scheduleItem)" v-show="resumeGame(schedule.home[spring+n-1].scheduleItem,'h')">Resume Game</b-button>
          </span>
        </b-col>
        <b-col cols='6'>
          <!-- {{ schedule.away[spring+n-1] }} -->
          <span class='text-monospace pl-5'>
            <span v-html="teamName(schedule.away[spring+n-1].scheduleItem.homeTeam)"></span>
            ({{ schedule.away[spring+n-1].scheduleItem.numberOfGames }})
            <span v-html="results(schedule.away[spring+n-1].scheduleItem,'a')"></span>
            <b-button variant='primary' size='sm' href='#' v-on:click="doStartSeries(schedule.home[n-1].scheduleItem)" v-show="startSeries(schedule.away[spring+n-1].scheduleItem,'a')">Start Series</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doNextGame(schedule.home[n-1].scheduleItem)" v-show="nextGame(schedule.away[spring+n-1].scheduleItem,'a')">Next Game</b-button>
            <b-button variant='primary' size='sm' href='#' v-on:click="doResumeGame(schedule.home[n-1].scheduleItem)" v-show="resumeGame(schedule.away[spring+n-1].scheduleItem,'a')">Resume Game</b-button>
          </span>
        </b-col>
      </b-row>
      <!-- {{ schedule }} -->
      <b-modal ref='startGameModal' hide-footer title='Start Game'>
        <b-row>
          <b-col cols='6' class='text-right pr-3'>
            <label for='myGame'>{{ team.city }} Game Number: </label>
          </b-col>
          <b-col cols='6'>
            <b-form-input id='myGame' type='text' v-model='gameNumber'></b-form-input>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols='6' class='text-right pr-3'>
            <label for='oppGame'>{{ opponent.city }} Game Number: </label>
          </b-col>
          <b-col cols='6'>
            <b-form-input id='oppGame' type='text' v-model='opponent.gameNumber'></b-form-input>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols='6' class='text-right pr-3'>
            <label for='gameDate'>Date: </label>
          </b-col>
          <b-col cols='6'>
            <b-form-input id='gameDate' type='text' v-model='gameDate'></b-form-input>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols='6' class='text-right pr-3'>
            <label for='gameDate'>Weather: </label>
          </b-col>
          <b-col cols='6'>
            <b-form-select v-model='weather' 
                           v-bind:options='weatherChoices' >
            </b-form-select>
          </b-col>
        </b-row>
        <b-row class='pt-1'>
          <b-col cols='12' class='text-right pr-3'>
            <b-button variant='primary' size='sm' href='#' v-on:click="startGame()">Start Game</b-button>
          </b-col>
        </b-row>
      </b-modal>
    </div>
  `,
  data() {
    return {
      team: {"team_name":""},
      teamname: 't',
      schedule: {},
      gameNumber: 0,
      spring: 0,
      fall: 0,
      fetching: false,
      home: true,
      opponent: {
        'city':'',
        'team':'',
        'gameNumber': 0,
      },
      gameDate: '',
      weather: 'Average',
      weatherChoices: [
        { value: 'Good', text: 'Good' },
        { value: 'Average', text: 'Average' },
        { value: 'Bad', text: 'Bad' },
      ],
    }
  },
  watch: {
    schedule() {
      for (si of this.schedule.home) {
        if (si.scheduleItem.season == '0') this.spring++;
        else this.fall ++;
      }
      //console.log(this.spring + ' - ' + this.fall);
    },
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    eBus.$on('scheduleUpdated',(s) => { this.schedule=s; this.gameNumber = vue.game; })
    if (this.teamname == 't' && vue != undefined) vue.emitData();
    d = new Date();
    this.gameDate = (d.getMonth()+1) + '/' + d.getDate() + '/' + d.getFullYear();
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    rosterValid() { if (vue == undefined) return false; return vue.rosterValid(); },
    year() { if (vue == undefined) return ''; return vue.year; },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
    teamName(team) {
      rtn = team;
      for (t of vue.cyConfig.teams) {
        //console.log(t);
        if (t.team == team) rtn=t.city;
      }
      for (let i=rtn.length; i < 16; i++) rtn += "&nbsp;";
      return rtn;
    },
    results(si,ha) {
      rtn = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
      return rtn;
    },
    startSeries(si,ha) {
      if (! this.rosterValid()) return false;
      if (vue == undefined) return false;
      if (! vue.betweenSeries) return false;
      if (vue.gameInProgress) return false;
      if (si.results.length != 0) return false;
      return true;
    },
    startGame() {
      this.$refs['startGameModal'].hide();
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/putStartGame.php',{data: { 'year':vue.year,
                                                     'away':(this.home?this.opponent.team:this.team.team),
                                                     'agame':(this.home?this.opponent.gameNumber:this.gameNumber),
                                                     'home':(this.home?this.team.team:this.opponent.team),
                                                     'hgame':(this.home?this.gameNumber:this.opponent.gameNumber),
                                                     'date':this.gameDate,
                                                     'weather':this.weather.toLowerCase()}}
                ,headers)
        .then(function (response) {
          vue.loadGameInfo();
        })
        .catch(function (error) {
          console.error(error);
      });
    },
    doStartSeries(si) {
      if (si.homeTeam == this.team.team) {
        this.opponent.team = si.awayTeam;
        this.home = true;
      } else { 
        this.opponent.team = si.homeTeam;
        this.home = false;
      }
      for (t of vue.cyConfig.teams) {
        if (t.team == this.opponent.team) this.opponent.city=t.city;
      }
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.get('/pss/api/getGameNumber.php?team='+this.opponent.team+'&year='+vue.year,headers)
        .then(function (response) {
          let d = response.data;
          //console.log(d);
          //console.log(d.gameNumber);
          self.opponent.gameNumber = d.gameNumber;
          //console.log(self.opponent);
          self.$refs['startGameModal'].show();
        })
        .catch(function (error) {
          console.error(error);
        });

    },
    nextGame(si,ha) {
      if (! this.rosterValid()) return false;
      if (vue == undefined) return false;
      if (vue.betweenSeries) return false;
      if (vue.gameInProgress) return false;
      if (si.results.length == 0) return false;
      if (si.results.length == si.numberOfGames) return false;
      return true;
    },
    doNextGame(si) {
      this.$refs['startGameModal'].show();
    },
    resumeGame(si,ha) {
      if (! this.rosterValid()) return false;
      if (vue == undefined) return false;
      if (vue.betweenSeries) return false;
      if (! vue.gameInProgress) return false;
      rl = si.results.length;
      if (rl == 0) return false;
      if (si.results[rl-1].final == true) return false;
      return true;
    },
    doResumeGame(si) {
      vue.loadGameInfo();
    },

  },
};

