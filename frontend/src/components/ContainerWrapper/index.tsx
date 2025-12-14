import type { BoxProps } from '@mui/material'
import { Box } from '@mui/material'

function ContainerWrapper(props: BoxProps<'form'>) {
  return (
    <Box
      style={{
        height: 'calc(100vh - 64px)',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
      }}
    >
      {props.children}
    </Box>
  )
}

export default ContainerWrapper
