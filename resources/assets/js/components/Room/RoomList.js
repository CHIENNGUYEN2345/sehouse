import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Room from './Room';

class RoomList extends Component {

    constructor(props) {
        super(props);
        this.state = {
            rooms: []
        };
    }

    componentDidMount() {
        axios.get("https://airbnb.kennjdemo.com/api/v1/rooms/list")
            .then(response => {
                this.setState({ rooms: response.data.data });
            })
            .catch(err => console.log(err));
    }

    render() {
        return (
            <div>
                <Room rooms={this.state.rooms} />
            </div>
        )
    }
}
export default RoomList;

if (document.getElementById('RoomList')) {
    ReactDOM.render(<RoomList />, document.getElementById('RoomList'));
}
