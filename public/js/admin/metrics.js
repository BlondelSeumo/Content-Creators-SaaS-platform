"use strict";

/**
 * @param form  The form to be submitted
 * @param ts    Thousands separator
 */
// eslint-disable-next-line no-unused-vars
let getCardValue = (form, ts) =>
{
    hideCardElements(form.querySelectorAll('[data-card-value], [data-card-status-increase], [data-card-status-decrease], [data-card-status-npd], [data-card-status-ncd], [data-card-status-constant], [data-card-status-error]'));
    showCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));

    const data = new URLSearchParams();

    let inputs = form.elements;

    // eslint-disable-next-line no-cond-assign
    for (let i = 0, element; element = inputs[i++];) {
        data.append(element.name, element.value);
    }

    fetch(form.action + '?' + data.toString(), {
        'method' : 'GET'
    })
        .then(response => {
            if(!response.ok) {
                throw response;
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw ({'statusText' : data.error});
            }
            let value = data.value;
            let previous = data.previous_value;
            let growth = data.growth;

            if (growth > 0) {
                form.querySelector('[data-card-increase-growth]').textContent = Number.parseFloat(Math.abs(growth).toFixed(2)).toString();
                showCardElements(form.querySelectorAll('[data-card-status-increase]'));
            } else if(growth < 0) {
                form.querySelector('[data-card-decrease-growth]').textContent = Number.parseFloat(Math.abs(growth).toFixed(2)).toString();
                showCardElements(form.querySelectorAll('[data-card-status-decrease]'));
            } else {
                if(value === previous && value > 0) {
                    showCardElements(form.querySelectorAll('[data-card-status-constant'));
                } else if (previous === 0 || previous === null) {
                    showCardElements(form.querySelectorAll('[data-card-status-npd]'));
                } else {
                    showCardElements(form.querySelectorAll('[data-card-status-ncd]'));
                }
            }

            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading], [data-card-status-error]'));
            showCardElements(form.querySelectorAll('[data-card-value]'));

            form.querySelector('[data-card-value]').textContent = data.value.format(0, 3, ts).toString();
        })
        .catch(error => {
            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));
            showCardElements(form.querySelectorAll('[data-card-status-error]'));
            form.querySelector('[data-card-status-error]').textContent = error.statusText;
        });
};

/**
 * @param form  The form to be submitted
 * @param chart The chart name
 * @param ts    Thousands separator
 * @param st    Show total value
 */
// eslint-disable-next-line no-unused-vars
let getCardTrend = (form, chart, ts, st) =>
{
    hideCardElements(form.querySelectorAll('[data-card-value], [data-card-status-error], [data-card-chart]'));
    showCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));

    const data = new URLSearchParams();

    let inputs = form.elements;

    // eslint-disable-next-line no-cond-assign
    for (let i = 0, element; element = inputs[i++];) {
        data.append(element.name, element.value);
    }

    fetch(form.action + '?' + data.toString(), {
        'method' : 'GET'
    })
        .then(response => {
            if(!response.ok) {
                throw response;
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw ({'statusText' : data.error});
            }

            // Empty any previous chart data
            chart.data.labels = [];
            chart.data.datasets[0].data = [];

            // Bind the new chart values
            let total = 0;
            for (const [key, value] of Object.entries(data.values)) {
                chart.data.labels.push(key);
                chart.data.datasets[0].data.push(value);
                total += value;
            }

            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading], [data-card-status-error]'));
            showCardElements(form.querySelectorAll('[data-card-chart]'));

            chart.update();

            // If show total value
            if (st) {
                showCardElements(form.querySelectorAll('[data-card-value]'));
                form.querySelector('[data-card-value]').textContent = total.format(0, 3, ts).toString();
            }
        })
        .catch(error => {
            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));
            showCardElements(form.querySelectorAll('[data-card-status-error]'));
            form.querySelector('[data-card-status-error]').textContent = error.statusText;
        });
};

/**
 * @param form  The form to be submitted
 * @param chart The chart name
 * @param ts    Thousands separator
 * @param color RGB values
 */
// eslint-disable-next-line no-unused-vars
let getCardPartition = (form, chart, ts, color) =>
{
    hideCardElements(form.querySelectorAll('[data-card-status-error], [data-card-chart], [data-card-legend]'));
    showCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));

    const data = new URLSearchParams();

    let inputs = form.elements;

    // eslint-disable-next-line no-cond-assign
    for (let i = 0, element; element = inputs[i++];) {
        data.append(element.name, element.value);
    }

    let legend = form.querySelector('[data-legend-placeholder]');

    fetch(form.action + '?' + data.toString(), {
        'method' : 'GET'
    })
        .then(response => {
            if(!response.ok) {
                throw response;
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw ({'statusText' : data.error});
            }

            // Empty any previous chart data
            chart.data.labels = [];
            chart.data.datasets[0].data = [];

            let
                legendEl = '',
                colors = [],
                alpha = 1,
                alphaStep = ((alpha*10) / Object.keys(data.values).length)/10;

            // Bind the new chart values
            for (const [key, value] of Object.entries(data.values)) {
                chart.data.labels.push(key);
                chart.data.datasets[0].data.push(value.format(0, 3, ts).toString());
                colors.push('rgba(' + color +', ' + alpha + ')');

                legendEl = legend.cloneNode(true);
                legendEl.classList.remove('d-none');
                legendEl.querySelector('.chart-legend').setAttribute('style', 'background: rgba(' + color +', ' + alpha + ')');
                legendEl.querySelector('[data-legend-name]').textContent = key;
                legendEl.querySelector('[data-legend-value]').textContent = parseFloat(value).format(0, 3, ts).toString();
                form.querySelector('[data-card-legend]').appendChild(legendEl);

                alpha = alpha-alphaStep;
                // eslint-disable-next-line no-unused-vars
            }

            chart.data.datasets[0].backgroundColor = colors;

            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading], [data-card-status-error]'));
            showCardElements(form.querySelectorAll('[data-card-chart], [data-card-legend]'));

            chart.update();
        })
        .catch(error => {
            hideCardElements(form.querySelectorAll('[data-card-loading], [data-card-status-loading]'));
            showCardElements(form.querySelectorAll('[data-card-status-error]'));
            form.querySelector('[data-card-status-error]').textContent = error.statusText;
        });
};

let hideCardElements = (elements) => {
    for (let i = 0; i < elements.length; i++) {
        elements[i].classList.add('d-none');
    }
};

let showCardElements = (elements) => {
    for (let i = 0; i < elements.length; i++) {
        elements[i].classList.remove('d-none');
    }
};

Number.prototype.format = function(n, x, s, c) {
    let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};
