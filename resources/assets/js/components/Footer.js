import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Footer extends Component {
    render() {
        return (
            <div className="container">
                This footer
            </div>
        );
    }
}

if (document.getElementById('AppFooter')) {
    ReactDOM.render(<Footer />, document.getElementById('AppFooter'));
}
