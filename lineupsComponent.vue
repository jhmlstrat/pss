var LineupsComponent = {
  name: 'LineupsComponent',
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
      <b-row>
        <b-col cols='12' class='text-center'>
          <span class='h3'>
            {{ teamInfo(gameInfo.visitor) }} at  {{ teamInfo(gameInfo.home) }}
          </span>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols='12' class='text-right'>
          <span v-html='weather(gameInfo)'></span>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols='12'>
          <b-tabs v-show="gameInfo != {}">
            <b-tab v-bind:title="gameInfo.visitor.name.toUpperCase()" v-bind:active="team.team == gameInfo.visitor.name">
              <lineup-component v-bind:side="gameInfo.visitor" v-bind:oside="gameInfo.home" v-bind:roster="vRoster()" v-bind:oroster="hRoster()" v-bind:rotation="vRotation()" v-bind:orotation="hRotation()" v-on:rotationUpdate="rotationUpdate('away',$event)" v-on:lineupUpdate="lineupUpdate('away',$event)"></lineup-component>
            </b-tab>
            <b-tab v-bind:title="gameInfo.home.name.toUpperCase()" v-bind:active="team.team == gameInfo.home.name">
              <lineup-component v-bind:side="gameInfo.home" v-bind:oside="gameInfo.visitor" v-bind:roster="hRoster()" v-bind:oroster="vRoster()" v-bind:rotation="hRotation()" v-bind:orotation="vRotation()" v-on:rotationUpdate="rotationUpdate('home',$event)" v-on:lineupUpdate="lineupUpdate('home',$event)"></lineup-component>
            </b-tab>
          </b-tabs>
        </b-col>
      </b-row>
    </div>
  `,
  data() {
    return {
      team: {"team_name":""},
      teamname: 't',
    }
  },
  components: {
    'lineup-component': LineupComponent,
  },
  watch: {
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
    if (this.teamname == 't' && vue != undefined) vue.emitData();
  },
  computed: {
    gameInfo() { if (vue == undefined) return {'visitor':{'name':'','gameNumber':''},'home':{'name':'','gameNumber':''}}; return vue.gameInfo; },
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    switchToMenu() {
      vue.currentComponent='menuComponent';
      vue.emitData();
    },
    teamInfo(s) {
      if (! vue.cyConfig.teams) return;
      city = '';
      nickname = ''
      gameNumber = '';
      if (s.name != '') {
        for (t of vue.cyConfig.teams) {
          if (t.team == s.name) {
            city=t.city;
            nickname=t.team_name;
          }
        }
        gameNumber = s.gameNumber;
      }
      return city + ' ' + nickname + ' ( ' + gameNumber + ' )';
    },
    hRoster() {
      if (this.team.team = vue.gameInfo.visitor.name) return vue.oRoster;
      return vue.roster;
    },
    hRotation() {
      if (this.team.team = vue.gameInfo.visitor.name) return vue.oRotation;
      return vue.rotation;
    },
    vRoster() {
      if (this.team.team = vue.gameInfo.visitor.name) return vue.roster;
      return vue.oRoster;
    },
    vRotation() {
      if (this.team.team = vue.gameInfo.visitor.name) return vue.rotation;
      return vue.oRotation;
    },
    weather(g) {
      rtn = '<span id="weather-string" v-b-tooltip.hover title="';
      if (g.weather && vue.cyConfig.teams) {
        for (t of vue.cyConfig.teams) {
          if (t.team == g.home.name) {
            rtn += 'BPS: ';
            rtn += t.weather.values[g.weather].bps.left;
            rtn += '/';
            rtn += t.weather.values[g.weather].bps.right;
            rtn += '; BPHR: ';
            rtn += t.weather.values[g.weather].bphr.left;
            rtn += '/';
            rtn += t.weather.values[g.weather].bphr.right;
          }
        }
      }
      rtn += '">Weather: ';
      if (g.weather) rtn += '<span class="text-capitalize">' + g.weather + '</span>';
      rtn += '</span>';
      return rtn;
    },
    lineupUpdate(side,event) {
      if (side == 'home') {
        vue.gameInfo.home.lineup = event;
      } else {
        vue.gameInfo.visitor.lineup = event;
      }
      var self = this;
      //console.log(vue.year);
      //console.log(vue.gameInfo);
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/updateLineup.php',{data: { 'year':vue.year,
                                                     'game':vue.gameInfo}}
                ,headers)
        .then(function (response) {
          vue.loadGameInfo();
        })
        .catch(function (error) {
          console.error(error);
      });
    },
    rotationUpdate(side,event) {
      if (side == 'home') {
        vue.gameInfo.home.rotation.push(event);
      } else {
        vue.gameInfo.visitor.rotation.push(event);
      }
      var self = this;
      let headers = {headers:{'X-Authorization':'TooManyMLs'}};
      axios.put('/pss/api/updateLineup.php',{data: { 'year':vue.year,
                                                     'game':vue.gameInfo}}
                ,headers)
        .then(function (response) {
          vue.loadGameInfo();
        })
        .catch(function (error) {
          console.error(error);
      });
    },
  },
};

