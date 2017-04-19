/**
 * Created by snowgirl on 4/14/17.
 */


/**
 * Very simple client app
 * @todo split into separate classes (entity, storage, fight strategy, skill strategy, view etc.)
 * @todo add error handlers
 * @todo remove code duplicates if exists
 * @todo implement promises
 * @todo cache heroes
 */
var gameApp = function () {
    this.iniArgs(arguments);
    this.syncClient(function () {
        this.iniDOM();
    });
};

gameApp.prototype.iniArgs = function (args) {
    this.view = $('#' + args[0]);
    this.api = args[1];
    this.fightRoundDelay = 2000;
    this.roundsPerFight = 5;
};
gameApp.prototype.iniDOM = function () {
    var client = this.getClient();
//    console.log('DOM: ', client);

    if (!client) {
        this.showViewPickName();
    } else if (!client.hero_id) {
        this.showViewPickHero();
    } else {
        this.syncGame(function (game) {
            if (game) {
                this.showViewFight();
            } else {
                this.showViewPickGame();
            }
        });
    }
};

gameApp.prototype.syncClient = function (callback) {
    if (this.getClient()) {
        this.request(['user', this.getClient().id].join('/'), 'get', {}, function (code, user) {
//            console.log('GET:', code, user);

            if ((200 == code) && user) {
                this.setClient(user);
            } else {
                this.clearClient();
            }

            $.proxy(callback, this)();
        });
    } else {
        $.proxy(callback, this)();
    }
};
gameApp.prototype.normalizeClient = function (client) {
    if (client.level) {
        client.level = parseInt(client.level);
    }

    return client;
};
gameApp.prototype.setClient = function (client) {
    return this.setCache('client', this.normalizeClient(client));
};
gameApp.prototype.getClient = function () {
    return this.getCache('client');
};
gameApp.prototype.clearClient = function () {
    return this.clearCache('client');
};
gameApp.prototype.onClientModify = function (data, callback) {
//    console.log('onClientModify: ', this.getClient());
    var uri;
    var method;

    if (this.getClient()) {
        uri = ['user', this.getClient().id].join('/');
        //we do partial updates only
        method = 'patch';
    } else {
        uri = 'user';
        //create
        method = 'post';
    }

    this.request(uri, method, data, function (code, user) {
        //@todo error processing
//        console.log('onClientModify - ' + method.toUpperCase() + ':', code, user);

        if ([200, 201].indexOf(code) > -1) {
            this.setClient(user);
            $.proxy(callback, this)();
        }
    });
};

gameApp.prototype.syncGame = function (callback) {
    if (this.getGame()) {
        this.request(['game', this.getGame().id].join('/'), 'get', {}, function (code, game) {
//            console.log('GET:', code, game);

            if ((200 == code) && game) {
                this.setGame(game);
                $.proxy(callback, this)(game);
            } else {
                this.clearGame();
                $.proxy(callback, this)();
            }
        });
    } else {
        $.proxy(callback, this)();
    }
};
gameApp.prototype.normalizeGame = function (game) {
    if (game.candidate) {
        game.candidate.level = parseInt(game.candidate.level);
    }

    if (game.log) {
        for (var i = 0, l = game.log.length; i < l; i++) {
            for (var j in game.log[i]) {
                if (game.log[i].hasOwnProperty(j)) {
                    game.log[i][j] = parseInt(game.log[i][j]);
                }
            }
        }
    }

    return game;
};
gameApp.prototype.setGame = function (game) {
    return this.setCache('game', this.normalizeGame(game));
};
gameApp.prototype.getGame = function () {
    return this.getCache('game');
};
gameApp.prototype.clearGame = function () {
    return this.clearCache('game');
};
gameApp.prototype.onGameModify = function (data, callback) {
//    console.log('onGameModify: ', this.getGame());
    var uri;
    var method;

    if (this.getGame()) {
        uri = ['game', this.getGame().id].join('/');
        //we do partial updates only
        method = 'patch';
    } else {
        uri = 'game';
        //create
        method = 'post';
        data['user_id'] = this.getClient().id;
    }

    this.request(uri, method, data, function (code, game) {
        //@todo error processing
//        console.log('onGameModify - ' + method.toUpperCase() + ':', code, game);

        if ([200, 201].indexOf(code) > -1) {
            this.setGame(game);
            $.proxy(callback, this)(this.getGame());
        }
    });
};
gameApp.prototype.onGameDelete = function (callback) {
    this.request(['game', this.getGame().id].join('/'), 'delete', {}, function (code) {
        //@todo error processing
        if (204 == code) {
            $.proxy(callback, this)();
        }
    });
};
gameApp.prototype.getClientName = function () {
    var client = this.getClient();
    return [
        client.name,
        '[' + client['hero'].name + ',',
        client.level + ' lvl.]'
    ].join(' ');
};

gameApp.prototype.request = function (uri, method, data, fn) {
        this.view.addClass('loading');

    return $.ajax({url: this.api + '/' + uri, dataType: 'json', type: method, data: data})
        .always($.proxy(function (response, code) {
                this.view.removeClass('loading');

            if (response) {
                code = response.hasOwnProperty('responseJSON') ? response['responseJSON']['code'] : response['code'];
                response = response.hasOwnProperty('responseJSON') ? response['responseJSON']['body'] : response['body'];
            } else if ('nocontent' == code) {
                code = 204;
            }

            $.proxy(fn, this)(code, response);
        }, this));
};

/**
 * Candidate factory
 */
gameApp.prototype.genCandidate = function (heroes) {
    var name = 'Player #' + Math.floor(new Date().getTime() / 1000);

    var heroesIds = [];

    for (var i = 0, l = heroes.length; i < l; i++) {
        heroesIds.push(heroes[i]['id']);
    }

    var heroIdIndex = this.getRand(0, heroesIds.length - 1);
    var heroId = heroesIds[heroIdIndex];

    var hero;

    for (i = 0; i < l; i++) {
        if (heroId == heroes[i]['id']) {
            hero = heroes[i];
        }
    }

    var clientLevel = this.getClient().level;
    var level = parseInt(clientLevel < 5 ? 0 : this.getRand(clientLevel - 5, clientLevel + 2));

    return {
        name: name,
        hero_id: heroId,
        hero: hero,
        level: level
    };
};
gameApp.prototype.getRand = function (min, max) {
    return Math.floor(Math.random() * max) + min;
};
gameApp.prototype.getGamesScore = function (game) {
    var log = game.log;
    var score = {client: 0, candidate: 0};

    for (var i = 0, l = log.length; i < l; i++) {
        var clientScore = log[i].client_attack - log[i].candidate_defence;
        var candidateScore = log[i].candidate_attack - log[i].client_defence;
//        score.client += log[i].client_attack;
//        score.client -= log[i].candidate_defence;
//        score.candidate += log[i].candidate_attack;
//        score.candidate -= log[i].client_defence;

        score.client += clientScore > 0 ? clientScore : 0;
        score.candidate += candidateScore > 0 ? candidateScore : 0;
    }

    return score;
};
gameApp.prototype.drawScores = function (game, $clientScore, $candidateScore) {
    var score = this.getGamesScore(game);
    var delta = score.client - score.candidate;

    $clientScore.removeClass('won lose')
        .addClass(0 == delta ? '' : delta > 0 ? 'won' : 'lose')
        .text(score.client);
    $candidateScore.removeClass('won lose')
        .addClass(0 == delta ? '' : delta > 0 ? 'lose' : 'won')
        .text(score.candidate);
};
gameApp.prototype.setRoundTimeout = function (callback) {
    this.roundTimeout = setTimeout($.proxy(callback, this), this.fightRoundDelay);
};
gameApp.prototype.unsetRoundTimeout = function () {
    if (this.hasOwnProperty('roundTimeout') && this.roundTimeout) {
        clearTimeout(this.roundTimeout);
    }
};
gameApp.prototype.genFightRound = function (client, candidate) {
    var getAttackStrategy = $.proxy(function () {
        return $.proxy(function (attack, level) {
            attack = attack + level;
            return this.getRand(attack - 3, attack + 3);
        }, this);
    }, this);

    var getDefenceStrategy = $.proxy(function () {
        return $.proxy(function (defence, level) {
            defence = defence + 2 * level;
            return this.getRand(defence - 6, defence + 6);
        }, this);
    }, this);

    return {
        client_attack: getAttackStrategy()(client['hero']['attack'], client.level),
        candidate_defence: getDefenceStrategy()(candidate['hero']['defence'], candidate.level),
        candidate_attack: getAttackStrategy()(candidate['hero']['attack'], candidate.level),
        client_defence: getDefenceStrategy()(client['hero']['defence'], client.level)
    };
};

gameApp.prototype.normalizeView = function (className) {
    this.view
        .removeAttr('class')
        .empty()
    ;

    if (this.getClient() || this.getGame()) {
        var $btnClearLocal = $('<button/>', {
            type: 'button',
            text: 'Clear'
        });

        $btnClearLocal.on('click', $.proxy(function () {
            this.clearClient();
            this.clearGame();
            this.iniDOM();
        }, this));

        this.view.append($btnClearLocal);
    }


    this.view.addClass(className)
//        .removeClass('loading')
    ;
};
gameApp.prototype.genHeroView = function (hero) {
    return $('<div/>', {class: 'hero', 'data-id': hero.id})
        .append($('<div/>', {class: 'hero-name', text: hero.name}))
        //@todo properties into separate key and do loop
        .append($('<div/>', {class: 'hero-attack', html: 'Attack: <b>' + hero['attack'] + '</b>'}))
        .append($('<div/>', {class: 'hero-defence', html: 'Defence: <b>' + hero['defence'] + '</b>'}));
};
gameApp.prototype.genUserView = function (user, isClient) {
    return $('<div/>', {class: 'user'})
        .append($('<div/>', {class: 'name', html: user.name + (isClient ? ' (you)' : '')}))
        .append($('<div/>', {class: 'hero'}).append(this.genHeroView(user['hero'])))
        .append($('<div/>', {class: 'level', text: 'Level: ' + user.level}));
};
gameApp.prototype.showViewPickName = function () {
    this.normalizeView('pick-name');

    var $h2 = $('<h2/>', {text: 'Tell what\'s your name please'});

    this.view.append($h2);

    var $input = $('<input/>', {
        type: 'text',
        placeholder: 'Your Name'
    });

    $input.focus();

    var $btn = $('<button/>', {
        type: 'button',
        text: 'OK'
    });

    $input.on('keyup', $.proxy(function (ev) {
        var value = $(ev.target).val();

        if (0 == value.length) {
            $btn.attr('disabled', true);
        } else {
            $btn.attr('disabled', false);

            if (13 == ev.which) {
                $btn.trigger('click');
            }
        }
    }, this));

    $input.trigger('keyup');

    this.view.append($input);

    $btn.on('click', $.proxy(function () {
        var name = $input.val();
        this.onClientModify({name: name}, function () {
            this.iniDOM();
        });
    }, this));

    this.view.append($btn);
};
gameApp.prototype.showViewPickHero = function () {
    this.request('hero', 'get', {}, function (code, heroes) {
        //@todo error processing
        this.normalizeView('pick-hero');

        var $h2 = $('<h2/>', {html: '<b>' + this.getClient().name + '</b>, pick up your hero please'});

        this.view.append($h2);

        for (var i = 0, l = heroes.length; i < l; i++) {
            var $hero = this.genHeroView(heroes[i]);

            $hero.on('click', $.proxy(function (ev) {
                var heroId = $(ev.target).closest('.hero').attr('data-id');
                this.onClientModify({hero_id: heroId}, function () {
                    this.iniDOM();
                });
            }, this));

            this.view.append($hero);
        }
    });
};
gameApp.prototype.showViewFight = function () {
    this.normalizeView('fight');

    var client = this.getClient();
    var game = this.getGame();
    var candidate = game.candidate;

    var $h2 = $('<h2/>', {text: 'Fight is running...'});

    this.view.append($h2);

    var $control = $('<div/>', {class: 'control'});

    var $btnSave = $('<button/>', {
        type: 'button',
        text: 'Save'
    });

    $btnSave.on('click', $.proxy(function () {
        this.onGameModify({is_saved: 1}, function () {
            this.unsetRoundTimeout();
            this.clearGame();
            this.iniDOM();
        });
    }, this));

    $control.append($btnSave);

    var $btnQuit = $('<button/>', {
        type: 'button',
        text: 'Quit'
    });

    $btnQuit.on('click', $.proxy(function () {
        if (this.getGame()) {
            this.onGameDelete(function () {
                this.unsetRoundTimeout();
                this.clearGame();
                this.iniDOM();
            });
        } else {
            this.unsetRoundTimeout();
            this.clearGame();
            this.iniDOM();
        }
    }, this));

    $control.append($btnQuit);

    this.view.append($control);

    var $vs = $('<div/>', {class: 'vs'});

    this.$clientScore = $('<div/>', {class: 'score', text: 0});

    $vs.append(this.$clientScore);

    var $client = this.genUserView(client, true);

    $vs.append($client);

    $vs.append($('<div/>', {text: 'vs'}));

    var $candidate = this.genUserView(candidate);

    $vs.append($candidate);

    this.$candidateScore = $('<div/>', {class: 'score', text: 0});

    $vs.append(this.$candidateScore);

    this.view.append($vs);

    var $h3 = $('<h3/>', {text: 'Log'});

    this.view.append($h3);

    this.$log = $('<div/>', {class: 'log'});

    this.view.append(this.$log);

    var isFinished = false;

    if (this.roundsPerFight == game.log.length) {
        isFinished = true;
    }

    var fn = $.proxy(function (roundNumber) {
        this.showViewFightRound(function (delta) {
            $h2.text('Fight is over!');

            var $result = $('<span/>', {class: 'result'});
            $h2.append($result);

            $btnSave.remove();

            if (!isFinished) {
                if (delta < 0) {
                    this.clearGame();
                    $result.addClass('lose').text('You lose!');
                } else {
                    var isWon = delta > 0;
                    var levelsUp = isWon ? 2 : 1;
                    this.onClientModify({level: this.getClient().level + levelsUp}, function () {
                        this.onGameDelete(function () {
                            this.clearGame();
                            $result.addClass(isWon ? 'won' : '')
                                .text((isWon ? 'You won' : 'Dead Heat') + '! +' + levelsUp + ' level' + (levelsUp > 1 ? 's' : ''));
                        });
                    });
                }
            }
        }, roundNumber);
    }, this);

    if (0 == game.log.length) {
        setTimeout(fn, this.fightRoundDelay);
    } else {
        fn();
    }
};
gameApp.prototype.showViewFightRound = function (onLastCallback, roundNumber) {
    if (!roundNumber) {
        roundNumber = 0;
    }

    var game = this.getGame();
    var log = game.log;

    if (roundNumber >= this.roundsPerFight) {
        var score = this.getGamesScore(game);
        return $.proxy(onLastCallback, this)(score.client - score.candidate);
    }

    var candidate = game.candidate;
    var client = this.getClient();
    var drawLog = $.proxy(function (number, round) {
        var $logRound = $('<div/>', {class: 'log-round'});

        var $logRoundTitle = $('<div/>', {class: 'log-round-title', text: 'Round #' + number});
//        $logRound.append($logRoundTitle);

        var $logRoundBody = $('<div/>', {class: 'log-round-body'});

        var $logRoundClient = $('<div/>', {class: 'log-round-client'})
            .append($('<div/>', {text: 'Attack: ' + round.client_attack}))
            .append($('<div/>', {text: 'Defence: ' + round.client_defence}));
        $logRoundBody.append($logRoundClient);

        $logRoundBody.append($logRoundTitle);

        var $logRoundCandidate = $('<div/>', {class: 'log-round-candidate'})
            .append($('<div/>', {text: 'Defence: ' + round.candidate_defence}))
            .append($('<div/>', {text: 'Attack: ' + round.candidate_attack}));
        $logRoundBody.append($logRoundCandidate);

        $logRound.append($logRoundBody);

        this.$log.append($logRound);
    }, this);

//    console.log('showViewFight - Client: ', client);
//    console.log('showViewFight - Candidate: ', candidate);

    if ('undefined' == typeof log[roundNumber]) {
        var round = this.genFightRound(client, candidate);
//    console.log('Round:', round);
        log.push(round);

        this.onGameModify({log: log}, function (game) {
            var log = game.log;
            var round = log[log.length - 1];

            drawLog(roundNumber + 1, round);
            this.drawScores(game, this.$clientScore, this.$candidateScore);

            this.setRoundTimeout(function () {
                this.showViewFightRound(onLastCallback, ++roundNumber);
            });
        });
    } else {
        drawLog(roundNumber + 1, log[roundNumber]);
        this.drawScores(game, this.$clientScore, this.$candidateScore);
        this.showViewFightRound(onLastCallback, ++roundNumber);
    }
};
gameApp.prototype.showViewPickGame = function () {
    //@todo implement promises
    this.request('hero', 'get', {}, function (code, heroes) {
        this.normalizeView('pick-game');

        var $new = $('<div/>', {class: 'new'});

        this.view.append($new);

        var $saved = $('<div/>', {class: 'saved'});

        this.view.append($saved);

        //@todo error processing
//        this.normalizeView('pick-game');

        var $h2 = $('<h2/>', {html: '<b>' + this.getClientName() + '</b>, we have nice candidate to fight over here'});

        $new.append($h2);

        var candidate = this.genCandidate(heroes);
        var $candidate = this.genUserView(candidate);

        $new.append($candidate);

        var $control = $('<div/>', {class: 'control'});

        var $btnPlay = $('<button/>', {
            type: 'button',
            text: 'Play'
        });

        $btnPlay.on('click', $.proxy(function () {
            this.onGameModify({candidate: candidate}, function () {
                this.showViewFight();
            });
        }, this));

        $control.append($btnPlay);

        var $btnChangeCandidate = $('<button/>', {
            type: 'button',
            text: 'Change'
        });

        $btnChangeCandidate.on('click', $.proxy(function () {
            this.iniDOM();
        }, this));

        $control.append($btnChangeCandidate);

        $new.append($control);

        this.request('game', 'get', {user_id: this.getClient().id, is_saved: 1}, function (code, games) {
            //@todo error processing

            if (0 == games.length) {
                return true;
            }

            var $h2 = $('<h2/>', {text: '..or continue playing some of your saved games'});
            $saved.append($h2);

            for (var id in games) {
                if (games.hasOwnProperty(id)) {
                    var game = games[id];

                    var $game = $('<div/>', {class: 'game', 'data-id': id});

                    var score = this.getGamesScore(game);

                    var $score = $('<div/>', {class: 'score-overview'});

                    var $clientScore = $('<div/>', {class: 'score', text: 0});
                    $score.append($clientScore);

                    var $candidateScore = $('<div/>', {class: 'score', text: 0});
                    $score.append($candidateScore);

                    $game.append($score);

                    var $candidate = this.genUserView(game.candidate);

                    $game.append($candidate);

                    var $btnContinue = $('<button/>', {
                        type: 'button',
                        text: 'Resume'
                    });

                    $btnContinue.on('click', $.proxy(function (ev) {
                        var gameId = $(ev.target).closest('.game').attr('data-id');
                        this.setGame({id: gameId});
                        this.syncGame(function () {
                            this.iniDOM();
                        });
                    }, this));

                    $game.append($btnContinue);

                    $saved.append($game);
                    this.drawScores(game, $clientScore, $candidateScore);
                }
            }
        });
    });
};

gameApp.prototype.setCache = function (k, v) {
    this[k] = v;

    var json = JSON.stringify(v);

    if (sessionStorage) {
        sessionStorage.setItem(k, json);
    }

    if (localStorage) {
        localStorage.setItem(k, json);
    }
};
gameApp.prototype.getCache = function (k) {
    if (this.hasOwnProperty(k) && this[k]) {
        return this[k];
    }

    var v;

    if (!v) {
        if (localStorage && (v = localStorage.getItem(k))) {
            v = JSON.parse(v);
        }
    }

    if (!v) {
        if (sessionStorage && (v = sessionStorage.getItem(k))) {
            v = JSON.parse(v);
        }
    }

    if (v) {
        this[k] = v;
    }

    return v;
};
gameApp.prototype.clearCache = function (k) {
    this[k] = null;
    if (sessionStorage) {
        sessionStorage.removeItem(k);
    }
    if (localStorage) {
        localStorage.removeItem(k);
    }
};