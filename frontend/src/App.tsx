import { Route, BrowserRouter as Router, Routes } from 'react-router-dom'
import NewUser from './pages/newUser'

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/newUser" element={<NewUser />} />
      </Routes>
    </Router>
  )
}

export default App
